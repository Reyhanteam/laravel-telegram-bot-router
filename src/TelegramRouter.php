<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if (!$this->request) {
            return response()->json(['error' => 'HTTP request is not available'], 500);
        }

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

        try {
            $this->dispatch($this->update);
        } catch (Throwable $e) {
            Log::error('Telegram Router Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function dispatch(TelegramUpdate $update): void
    {
        $routes = TelegramBot::getRoutes();

        foreach ($routes as $route) {
            if (!$this->routeMatches($route, $update)) {
                continue;
            }

            $this->execute($route, $update);
            return;
        }

        if ($fallback = TelegramBot::getFallback()) {
            $this->execute([
                'callback' => $fallback,
                'pattern' => 'fallback',
            ], $update);
            return;
        }

        if ($onInvalid = TelegramBot::getOnInvalid()) {
            $this->execute([
                'callback' => $onInvalid,
                'pattern' => 'onInvalid',
            ], $update);
            return;
        }

        Log::info('No matching route found', [
            'update_type' => $this->getUpdateType($update),
        ]);
    }

    protected function routeMatches(array $route, TelegramUpdate $update): bool
    {
        $type = $route['type'] ?? null;
        $pattern = $route['pattern'] ?? null;

        switch ($type) {
            case 'callback_query':
                if (!isset($update->callback_query)) {
                    return false;
                }

                // onCallbackQuery() without a pattern matches every callback query.
                if ($pattern === null || $pattern === '') {
                    return true;
                }

                return $this->matchPattern($pattern, (string) ($update->callback_query->data ?? ''), $update);

            case 'command':
                if (!isset($update->message->text)) {
                    return false;
                }

                $text = trim((string) $update->message->text);
                if (!str_starts_with($text, '/')) {
                    return false;
                }

                $command = explode(' ', $text, 2)[0];
                if (str_contains($command, '@')) {
                    $command = explode('@', $command, 2)[0];
                }

                return $command === $pattern;

            case 'text':
                if (!isset($update->message->text)) {
                    return false;
                }

                return $this->matchPattern(
                    (string) $pattern,
                    trim((string) $update->message->text),
                    $update
                );
        }

        return false;
    }

    protected function matchPattern(string $pattern, string $text, TelegramUpdate $update): bool
    {
        $pattern = trim($pattern);
        $text = trim($text);

        if ($this->isRegex($pattern)) {
            $result = preg_match($pattern, $text, $matches);

            if ($result === 1) {
                $update->matches = $matches;
                return true;
            }

            if ($result === false) {
                Log::warning('Invalid Telegram route regex', [
                    'pattern' => $pattern,
                    'error' => preg_last_error_msg(),
                ]);
            }

            return false;
        }

        return $pattern === $text;
    }

    protected function isRegex(string $pattern): bool
    {
        if (strlen($pattern) < 3) {
            return false;
        }

        $delimiter = $pattern[0];

        return $delimiter === substr($pattern, -1)
            && !ctype_alnum($delimiter)
            && $delimiter !== '\\';
    }

    protected function execute(array $route, TelegramUpdate $update): void
    {
        try {
            $this->resolveAction($route['callback'], $update);
        } catch (Throwable $e) {
            Log::error('Route execution failed', [
                'pattern' => $route['pattern'] ?? 'fallback',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function resolveAction($action, TelegramUpdate $update)
    {
        if ($action instanceof \Closure) {
            return $action($update);
        }

        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;

            if (!is_string($controller) || !class_exists($controller)) {
                throw new \InvalidArgumentException(
                    "Telegram route controller [{$controller}] was not found. Check routes/bot.php."
                );
            }

            if (!method_exists($controller, $method)) {
                throw new \InvalidArgumentException(
                    "Telegram route method [{$method}] was not found on [{$controller}]."
                );
            }

            $instance = app()->make($controller);

            return app()->call([$instance, $method], [
                'update' => $update,
            ]);
        }

        throw new \InvalidArgumentException('Invalid Telegram route action provided.');
    }

    protected function getUpdateType(TelegramUpdate $update): string
    {
        if (isset($update->callback_query)) {
            return 'callback_query';
        }

        if (isset($update->message->text)) {
            return str_starts_with((string) $update->message->text, '/')
                ? 'command'
                : 'text';
        }

        return 'unknown';
    }
}
