<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\Conversation\ConversationManager;
use ReyhanTeam\TelegramBotRouter\Events\CallbackQueryReceived;
use ReyhanTeam\TelegramBotRouter\Events\CommandReceived;
use ReyhanTeam\TelegramBotRouter\Events\MessageReceived;
use ReyhanTeam\TelegramBotRouter\Events\RouteMatched;
use ReyhanTeam\TelegramBotRouter\Events\UpdateReceived;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramExceptionHandler;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramRouteException;
use ReyhanTeam\TelegramBotRouter\Jobs\ProcessTelegramUpdateJob;
use ReyhanTeam\TelegramBotRouter\Middleware\MiddlewarePipeline;
use Throwable;

class TelegramRouter
{
    protected ?Request $request;
    protected TelegramUpdate $update;

    public function __construct(?Request $request = null) { $this->request = $request; }

    public function handle()
    {
        if (!$this->request) return response()->json(['error' => 'HTTP request is not available'], 500);
        $data = json_decode($this->request->getContent());
        if (!is_object($data) || json_last_error() !== JSON_ERROR_NONE) {
            $exception = new \ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException('Invalid JSON received from Telegram.');
            $this->handleException($exception, ['source' => 'router']);
            return response()->json(['error' => 'Invalid Telegram update'], 400);
        }
        if (!isset($data->update_id)) {
            $exception = new \ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException('Telegram update_id is missing.');
            $this->handleException($exception, ['source' => 'router']);
            return response()->json(['error' => 'Invalid Telegram update'], 400);
        }
        $this->update = new TelegramUpdate($data);
        TelegramBot::setApplication(app());
        event(new UpdateReceived($this->update));
        if (isset($this->update->message)) {
            event(new MessageReceived($this->update));
            if (isset($this->update->message->text) && str_starts_with(trim((string) $this->update->message->text), '/')) event(new CommandReceived($this->update));
        }
        if (isset($this->update->callback_query)) event(new CallbackQueryReceived($this->update));
        try { $this->dispatch($this->update); } catch (Throwable $e) { $this->handleException($e, ['source' => 'router']); }
        return response()->json(['ok' => true]);
    }

    public function handleException(Throwable $exception, array $context = []): void
    {
        $handlerClass = config('telegram-bot-router.exceptions.handler', TelegramExceptionHandler::class);
        app()->make($handlerClass)->handle($exception, $context);
    }

    public function dispatch(TelegramUpdate $update): void
    {
        $update->matches = null; $update->routeParameters = []; $update->commandArguments = [];
        $conversationManager = app(ConversationManager::class);
        if ($this->isConversationCancelCommand($update)) { $conversationManager->cancel($update); return; }
        if ($conversationManager->active($update)) {
            $this->execute(['callback' => fn(TelegramUpdate $update) => $conversationManager->handle($update), 'pattern' => 'conversation', 'middleware' => [], 'constraints' => [], 'rate_limits' => [], 'queue' => ['enabled' => false]], $update);
            return;
        }
        $matchedRoute = null; $matchedScore = -1; $matchedMatches = null; $matchedRouteParameters = []; $matchedCommandArguments = [];
        foreach (TelegramBot::getRoutes() as $route) {
            $update->matches = null; $update->routeParameters = []; $update->commandArguments = [];
            $score = $this->routeMatchScore($route, $update);
            if ($score > $matchedScore) { $matchedRoute = $route; $matchedScore = $score; $matchedMatches = $update->matches; $matchedRouteParameters = $update->routeParameters; $matchedCommandArguments = $update->commandArguments; }
        }
        if ($matchedRoute !== null) {
            $update->matches = $matchedMatches; $update->routeParameters = $matchedRouteParameters; $update->commandArguments = $matchedCommandArguments;
            event(new RouteMatched($update, $matchedRoute));
            $this->execute($matchedRoute, $update);
            return;
        }
        if ($onInvalid = TelegramBot::getOnInvalid()) { $this->execute(['callback' => $onInvalid, 'pattern' => 'onInvalid', 'middleware' => [], 'constraints' => [], 'rate_limits' => [], 'queue' => ['enabled' => false]], $update); return; }
        Log::info('No matching route found', ['update_type' => $this->getUpdateType($update)]);
    }

    protected function isConversationCancelCommand(TelegramUpdate $update): bool
    {
        if (!isset($update->message->text)) return false;
        $text = trim((string) $update->message->text); if ($text === '' || !str_starts_with($text, '/')) return false;
        $parts = preg_split('/\s+/', $text, 2); $command = $parts[0] ?? ''; if (str_contains($command, '@')) $command = explode('@', $command, 2)[0];
        return in_array($command, TelegramBot::getConversationCancelCommands(), true);
    }

    protected function routeMatches(array $route, TelegramUpdate $update): bool { return $this->routeMatchScore($route, $update) >= 0; }

    protected function routeMatchScore(array $route, TelegramUpdate $update): int
    {
        $type = $route['type'] ?? null; $pattern = $route['pattern'] ?? null; $constraints = $route['constraints'] ?? []; $parameters = $route['parameters'] ?? [];
        switch ($type) {
            case 'callback_query':
                if (!isset($update->callback_query)) return -1;
                if ($pattern === null || $pattern === '') return empty($constraints) ? 10 : -1;
                return $this->patternScore($pattern, (string) ($update->callback_query->data ?? ''), $update, $constraints);
            case 'command':
                if (!isset($update->message->text)) return -1;
                $text = trim((string) $update->message->text); if ($text === '' || !str_starts_with($text, '/')) return -1;
                $parts = preg_split('/\s+/', $text, 2); $command = $parts[0] ?? ''; $argumentText = $parts[1] ?? '';
                if (str_contains($command, '@')) { $command = explode('@', $command, 2)[0]; $normalizedText = $command . ($argumentText === '' ? '' : ' ' . $argumentText); } else $normalizedText = $text;
                if (!empty($parameters)) { $match = $this->matchRouteParameters($pattern, $normalizedText, $constraints); if ($match === null) return -1; $update->routeParameters = $match; $update->commandArguments = $argumentText === '' ? [] : preg_split('/\s+/', $argumentText); return 75; }
                if ($command !== $pattern) return -1; $update->commandArguments = $argumentText === '' ? [] : preg_split('/\s+/', $argumentText); return empty($constraints) ? 100 : -1;
            case 'text':
                if (!isset($update->message->text)) return -1; $text = trim((string) $update->message->text);
                if (!empty($parameters)) { $match = $this->matchRouteParameters($pattern, $text, $constraints); if ($match === null) return -1; $update->routeParameters = $match; return 75; }
                return $this->patternScore($pattern, $text, $update, $constraints);
        }
        return -1;
    }

    protected function matchRouteParameters(?string $pattern, string $text, array $constraints): ?array
    {
        if ($pattern === null || $this->isRegex($pattern)) return null;
        $names = []; $compiled = ''; $offset = 0;
        preg_match_all('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', $pattern, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] ?? [] as $index => $placeholder) { $token = $placeholder[0]; $position = $placeholder[1]; $name = $matches[1][$index][0]; $compiled .= preg_quote(substr($pattern, $offset, $position - $offset), '/'); $compiled .= '(?P<' . $name . '>[^\\s]+)'; $names[] = $name; $offset = $position + strlen($token); }
        if ($names === []) return null; $compiled .= preg_quote(substr($pattern, $offset), '/');
        $result = @preg_match('/^' . $compiled . '$/u', $text, $routeMatches); if ($result !== 1 || !$this->constraintsMatch($constraints, $routeMatches)) return null;
        $parameters = []; foreach ($names as $name) $parameters[$name] = $routeMatches[$name]; return $parameters;
    }

    protected function patternScore(?string $pattern, string $text, TelegramUpdate $update, array $constraints = []): int
    {
        if ($pattern === null) return -1; $pattern = trim($pattern); $text = trim($text);
        if (!$this->isRegex($pattern)) { if ($pattern !== $text || !empty($constraints)) return -1; return 100; }
        $result = @preg_match($pattern, $text, $matches); if ($result === 1) { if (!$this->constraintsMatch($constraints, $matches)) return -1; $update->matches = $matches; return 50; }
        if ($result === false) Log::warning('Invalid Telegram route regex', ['pattern' => $pattern, 'error' => preg_last_error_msg()]); return -1;
    }

    protected function constraintsMatch(array $constraints, array $matches): bool
    {
        foreach ($constraints as $name => $expression) { if (!array_key_exists($name, $matches)) return false; if (@preg_match('/^(?:' . $expression . ')$/u', (string) $matches[$name]) !== 1) return false; }
        return true;
    }

    protected function matchPattern(string $pattern, string $text, TelegramUpdate $update): bool { return $this->patternScore($pattern, $text, $update) >= 0; }

    protected function isRegex(string $pattern): bool
    {
        if (strlen($pattern) < 3) return false; $delimiter = $pattern[0]; if (ctype_alnum($delimiter) || $delimiter === '\\') return false;
        $length = strlen($pattern); $escaped = false;
        for ($i = 1; $i < $length; $i++) { $char = $pattern[$i]; if ($escaped) { $escaped = false; continue; } if ($char === '\\') { $escaped = true; continue; } if ($char === $delimiter) { $modifiers = substr($pattern, $i + 1); return $modifiers === '' || preg_match('/^[a-zA-Z]*$/', $modifiers) === 1; } }
        return false;
    }

    protected function execute(array $route, TelegramUpdate $update): void
    {
        try {
            $middleware = TelegramBot::getGlobalMiddleware();
            $configuredLimits = config('telegram-bot-router.rate_limit.limits', []); $routeLimits = $route['rate_limits'] ?? [];
            $limits = array_replace(is_array($configuredLimits) ? $configuredLimits : [], $routeLimits);
            if ((config('telegram-bot-router.rate_limit.enabled', false) || !empty($routeLimits)) && !empty($limits)) array_unshift($middleware, new \ReyhanTeam\TelegramBotRouter\Middleware\TelegramRateLimitMiddleware($limits, $route));
            $middleware = array_merge($middleware, $route['middleware'] ?? []);
            $destination = function (TelegramUpdate $update) use ($route): mixed {
                $queueConfig = $route['queue'] ?? [];
                $queueEnabled = (bool) ($queueConfig['enabled'] ?? false)
                    || (bool) config('telegram-bot-router.queue.updates', false);

                if (!$queueEnabled) {
                    return $this->resolveAction($route['callback'], $update);
                }

                if ($route['callback'] instanceof \Closure) {
                    throw new \InvalidArgumentException('Queued Telegram routes must use a controller action or another serializable action. Closures cannot be queued.');
                }

                $job = new ProcessTelegramUpdateJob(
                    $update->originalUpdate() instanceof \stdClass
                        ? json_decode(json_encode($update->originalUpdate()), true)
                        : (array) $update->originalUpdate(),
                    $route,
                );

                if (!empty($queueConfig['queue'])) {
                    $job->onQueue($queueConfig['queue']);
                }

                return dispatch($job);
            };
            (new MiddlewarePipeline($middleware))->process($update, $destination);
        } catch (TelegramRouteException $e) { throw $e; } catch (Throwable $e) { throw new TelegramRouteException('Telegram route execution failed for [' . ($route['pattern'] ?? 'fallback') . ']: ' . $e->getMessage(), $route['pattern'] ?? null, $route['callback'] ?? null, $e); }
    }

    public function processQueuedRoute(TelegramUpdate $update, array $route): void
    {
        TelegramBot::setApplication(app());
        $this->resolveAction($route['callback'], $update);
    }

    protected function resolveAction($action, TelegramUpdate $update)
    {
        if ($action instanceof \Closure) return $action($update, $update->commandArguments(), ...array_values($update->routeParameters));
        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            if (!is_string($controller) || !class_exists($controller)) throw new \InvalidArgumentException("Telegram route controller [{$controller}] was not found. Check routes/bot.php.");
            if (!is_string($method) || !method_exists($controller, $method)) throw new \InvalidArgumentException("Telegram route method [{$method}] was not found on [{$controller}].");
            $instance = app()->make($controller); $parameters = ['update' => $update, 'arguments' => $update->commandArguments()]; foreach ($update->routeParameters as $name => $value) $parameters[$name] = $value;
            return app()->call([$instance, $method], $parameters);
        }
        throw new \InvalidArgumentException('Invalid Telegram route action provided.');
    }

    protected function getUpdateType(TelegramUpdate $update): string
    {
        if (isset($update->callback_query)) return 'callback_query';
        if (isset($update->message->text)) return str_starts_with((string) $update->message->text, '/') ? 'command' : 'text';
        return 'unknown';
    }
}
