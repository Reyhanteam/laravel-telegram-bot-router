<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ReyhanTeam\TelegramBotRouter\Console\SetPostRouteCommand;
use ReyhanTeam\TelegramBotRouter\Console\StartPollingCommand;
use ReyhanTeam\TelegramBotRouter\Console\Commands\TelegramRouteCacheCommand;
use ReyhanTeam\TelegramBotRouter\Console\Commands\TelegramRouteClearCommand;
use ReyhanTeam\TelegramBotRouter\Console\Commands\TelegramRouteListCommand;
use ReyhanTeam\TelegramBotRouter\Core\UpdateManager;

class TelegramRouterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/telegram-bot-router.php',
            'telegram-bot-router'
        );

        $this->app->singleton(TelegramRouter::class, function () {
            return new TelegramRouter();
        });

        $this->app->alias(TelegramRouter::class, 'telegram.router');

        $this->commands([
            StartPollingCommand::class,
            SetPostRouteCommand::class,
            TelegramRouteListCommand::class,
            TelegramRouteCacheCommand::class,
            TelegramRouteClearCommand::class,
        ]);
    }

    public function boot(): void
    {
        $this->registerConfiguredMiddlewareAliases();
        $this->loadBotRoutes();

        if (config('telegram-bot-router.mode') === 'webhook') {
            Route::post(
                config('telegram-bot-router.webhook.path', '/telegram/webhook'),
                [UpdateManager::class, 'handleWebhook']
            );
        }

        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    protected function registerConfiguredMiddlewareAliases(): void
    {
        $aliases = config('telegram-bot-router.middleware.aliases', []);

        if (! is_array($aliases)) {
            return;
        }

        TelegramBot::aliasMiddlewares($aliases);
    }

    protected function loadBotRoutes(): void
    {
        $appRoutes = base_path('routes/bot.php');
        $packageRoutes = __DIR__ . '/../routes/bot.php';

        if (file_exists($appRoutes)) {
            require $appRoutes;
            return;
        }

        if (file_exists($packageRoutes)) {
            require $packageRoutes;
        }
    }

    protected function publishResources(): void
    {
        $this->publishes([
            __DIR__ . '/config/telegram-bot-router.php' => config_path('telegram-bot-router.php'),
        ], 'telegram-bot-config');

        $this->publishes([
            __DIR__ . '/../routes/bot.php' => base_path('routes/bot.php'),
        ], 'telegram-bot-routes');
    }
}
