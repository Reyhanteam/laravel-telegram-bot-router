<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Support\ServiceProvider;
use ReyhanTeam\TelegramBotRouter\Console\SetPostRouteCommand;
use Illuminate\Support\Facades\Route;

class TelegramRouterServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        // Merge package config with application config
        $this->mergeConfigFrom(
            __DIR__ . '/config/telegram-bot-router.php',
            'telegram-bot'
        );

        // Bind TelegramRouter as singleton
        $this->app->singleton(TelegramRouter::class, function ($app) {
            return new TelegramRouter($app['request']);
        });

        // Optional container alias
        $this->app->alias(TelegramRouter::class, 'telegram.router');


        $this->commands([
            \ReyhanTeam\TelegramBotRouter\Console\StartPollingCommand::class,
        ]);
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadBotRoutes();
        if (config('telegram-bot-router.mode') === 'webhook') {
        Route::post(
            config('telegram-bot.webhook.path'),
            [UpdateManager::class, 'handleWebhook']
        );
    }

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->publishResources();
        }
    }

    /**
     * Load bot route definitions.
     */
    protected function loadBotRoutes(): void
    {
        $appRoutes = base_path('routes/bot.php');
        $packageRoutes = __DIR__ . '/../routes/bot.php';

        // Application routes have priority
        if (file_exists($appRoutes)) {
            require $appRoutes;
            return;
        }

        // Fallback to package routes
        if (file_exists($packageRoutes)) {
            require $packageRoutes;
        }
    }

    /**
     * Register artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            SetPostRouteCommand::class,
        ]);
    }

    /**
     * Publish package resources.
     */
    protected function publishResources(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/config/telegram-bot-router.php' => config_path('telegram-bot-router.php'),
        ], 'telegram-bot-config');

        // Publish bot routes
        $this->publishes([
            __DIR__ . '/../routes/bot.php' => base_path('routes/bot.php'),
        ], 'telegram-bot-routes');
    }

}
