<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\Conversation\ConversationManager;
use ReyhanTeam\TelegramBotRouter\Middleware\MiddlewarePipeline;
use Throwable;

class TelegramRouter
{
    protected ?Request $request;
    protected TelegramUpdate $update;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function handle()
    {
        if (!$this->request) return response()->json(['error' => 'HTTP request is not available'], 500);
        $data = json_decode($this->request->getContent());
        if (!is_object($data) || json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON received from Telegram');
            return response()->json(['error' => 'Invalid JSON'], 400);
        }
        if (!isset($data->update_id)) {
            Log::warning('Telegram update without update_id');
            return response()->json(['ok' => true]);
        }
        $this->update = new TelegramUpdate($data);
        TelegramBot::setApplication(app());
        try { $this->dispatch($this->update); } catch (Throwable $e) {
            Log::error('Telegram Router Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
        return response()->json(['ok' => true]);
    }

    public function dispatch(TelegramUpdate $update): void
    {
        $update->matches = null;
        $update->routeParameters = [];
        $update->commandArguments = [];

        $conversationManager = app(ConversationManager::class);

        if ($conversationManager->active($update)) {
            $this->execute([
                'callback' => fn (TelegramUpdate $update) => $conversationManager->handle($update),
                'pattern' => 'conversation',
                'middleware' => [],
                'constraints' => [],
            ], $update);
            return;
        }

        $matchedRoute = null;
        $matchedScore = -1;

        foreach (TelegramBot::getRoutes() as $route) {
            $score = $this->routeMatchScore($route, $update);

            if ($score > $matchedScore) {
                $matchedRoute = $route;
                $matchedScore = $score;
            }
        }

        if ($matchedRoute !== null) {
            $this->execute($matchedRoute, $update);
            return;
        }

        if ($fallback = TelegramBot::getFallback()) {
            $this->execute(['callback' => $fallback, 'pattern' => 'fallback', 'middleware' => [], 'constraints' => []], $update);
            return;
        }
        if ($onInvalid = TelegramBot::getOnInvalid()) {
            $this->execute(['callback' => $onInvalid, 'pattern' => 'onInvalid', 'middleware' => [], 'constraints' => []], $update);
            return;
        }
        Log::info('No matching route found', ['update_type' => $this->getUpdateType($update)]);
    }

    protected function routeMatches(array $route, TelegramUpdate $update): bool
    {
        return $this->routeMatchScore($route, $update) >= 0;
    }

    protected function routeMatchScore(array $route, TelegramUpdate $update): int
    {
        $type = $route['type'] ?? null;
        $pattern = $route['pattern'] ?? null;
        $constraints = $route['constraints'] ?? [];
        $parameters = $route['parameters'] ?? [];

        switch ($type) {
            case 'callback_query':
                if (!isset($update->callback_query)) return -1;
                if ($pattern === null || $pattern === '') {
                    return empty($constraints) ? 10 : -1;
                }
                return $this->patternScore($pattern, (string) ($update->callback_query->data ?? ''), $update, $constraints);

            case 'command':
                if (!isset($update->message->text)) return -1;

                $text = trim((string) $update->message->text);
                if ($text === '' || !str_starts_with($text, '/')) return -1;

                $parts = preg_split('/\s+/', $text, 2);
                $command = $parts[0] ?? '';
                $argumentText = $parts[1] ?? '';

                if (str_contains($command, '@')) {
                    $command = explode('@', $command, 2)[0];
                }

                if (!empty($parameters)) {
                    $match = $this->matchRouteParameters($pattern, $text, $constraints);

                    if ($match === null) return -1;

                    $update->routeParameters = $match;
                    $update->commandArguments = $argumentText === ''
                        ? []
                        : preg_split('/\s+/', $argumentText);

                    return 75;
                }

                if ($command !== $pattern) return -1;

                $update->commandArguments = $argumentText === ''
                    ? []
                    : preg_split('/\s+/', $argumentText);

                return empty($constraints) ? 100 : -1;

            case 'text':
                if (!isset($update->message->text)) return -1;

                $text = trim((string) $update->message->text);

                if (!empty($parameters)) {
                    $match = $this->matchRouteParameters($pattern, $text, $constraints);

                    if ($match === null) return -1;

                    $update->routeParameters = $match;
                    return 75;
                }

                return $this->patternScore($pattern, $text, $update, $constraints);
        }

        return -1;
    }

    protected function matchRouteParameters(?string $pattern, string $text, array $constraints): ?array
    {
        if ($pattern === null || $this->isRegex($pattern)) {
            return null;
        }

        $compiled = preg_quote(trim($pattern), '/');
        $names = [];

        $compiled = preg_replace_callback(
            '/\\\\\{([A-Za-z_][A-Za-z0-9_]*)\\\\\}/',
            function (array $match) use (&$names): string {
                $name = $match[1];
                $names[] = $name;
                return '(?P<'.$name.'>[^\\s]+)';
            },
            $compiled
        );

        if ($compiled === null || $names === []) {
            return null;
        }

        $result = @preg_match('/^'.$compiled.'$/u', $text, $matches);

        if ($result !== 1) {
            return null;
        }

        if (!$this->constraintsMatch($constraints, $matches)) {
            return null;
        }

        $parameters = [];
        foreach ($names as $name) {
            $parameters[$name] = $matches[$name];
        }

        return $parameters;
    }

    protected function patternScore(?string $pattern, string $text, TelegramUpdate $update, array $constraints = []): int
    {
        if ($pattern === null) return -1;

        $pattern = trim($pattern);
        $text = trim($text);

        if (!$this->isRegex($pattern)) {
            if ($pattern !== $text || !empty($constraints)) return -1;
            return 100;
        }

        $result = @preg_match($pattern, $text, $matches);

        if ($result === 1) {
            if (!$this->constraintsMatch($constraints, $matches)) return -1;

            $update->matches = $matches;
            return 50;
        }

        if ($result === false) {
            Log::warning('Invalid Telegram route regex', [
                'pattern' => $pattern,
                'error' => preg_last_error_msg(),
            ]);
        }

        return -1;
    }

    protected function constraintsMatch(array $constraints, array $matches): bool
    {
        foreach ($constraints as $name => $expression) {
            if (!array_key_exists($name, $matches)) return false;

            $value = (string) $matches[$name];
            $result = @preg_match('/^(?:'.$expression.')$/u', $value);

            if ($result !== 1) return false;
        }

        return true;
    }

    protected function matchPattern(string $pattern, string $text, TelegramUpdate $update): bool
    {
        return $this->patternScore($pattern, $text, $update) >= 0;
    }

    protected function isRegex(string $pattern): bool
    {
        if (strlen($pattern) < 3) return false;
        $delimiter = $pattern[0];
        if (ctype_alnum($delimiter) || $delimiter === '\\') return false;
        $length = strlen($pattern); $escaped = false;
        for ($i = 1; $i < $length; $i++) {
            $char = $pattern[$i];
            if ($escaped) { $escaped = false; continue; }
            if ($char === '\\') { $escaped = true; continue; }
            if ($char === $delimiter) {
                $modifiers = substr($pattern, $i + 1);
                return $modifiers === '' || preg_match('/^[a-zA-Z]*$/', $modifiers) === 1;
            }
        }
        return false;
    }

    protected function execute(array $route, TelegramUpdate $update): void
    {
        try {
            $middleware = array_merge(TelegramBot::getGlobalMiddleware(), $route['middleware'] ?? []);
            $destination = fn (TelegramUpdate $update): mixed => $this->resolveAction($route['callback'], $update);
            (new MiddlewarePipeline($middleware))->process($update, $destination);
        } catch (Throwable $e) {
            Log::error('Route execution failed', ['pattern' => $route['pattern'] ?? 'fallback', 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function resolveAction($action, TelegramUpdate $update)
    {
        if ($action instanceof \Closure) {
            return $action($update, ...$update->commandArguments(), ...array_values($update->routeParameters));
        }

        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            if (!is_string($controller) || !class_exists($controller)) throw new \InvalidArgumentException("Telegram route controller [{$controller}] was not found. Check routes/bot.php.");
            if (!is_string($method) || !method_exists($controller, $method)) throw new \InvalidArgumentException("Telegram route method [{$method}] was not found on [{$controller}].");
            $instance = app()->make($controller);

            $parameters = [
                'update' => $update,
                'arguments' => $update->commandArguments(),
            ];

            foreach ($update->routeParameters as $name => $value) {
                $parameters[$name] = $value;
            }

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
