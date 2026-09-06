<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiMethodRegistry;
use ReyhanTeam\TelegramBotRouter\Events\CallbackQueryReceived;
use ReyhanTeam\TelegramBotRouter\Events\CommandReceived;
use ReyhanTeam\TelegramBotRouter\Events\MessageReceived;
use ReyhanTeam\TelegramBotRouter\Events\RouteMatched;
use ReyhanTeam\TelegramBotRouter\Events\UpdateReceived;
use ReyhanTeam\TelegramBotRouter\Facades\Telegram;
use ReyhanTeam\TelegramBotRouter\Jobs\ProcessTelegramUpdateJob;
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;
use ReyhanTeam\TelegramBotRouter\TelegramBot;
use ReyhanTeam\TelegramBotRouter\TelegramRouterServiceProvider;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use Tests\Fixtures\TelegramTestController;

final class Priority16FoundationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TelegramRouterServiceProvider::class];
    }

    public function test_package_services_are_registered(): void
    {
        $this->assertInstanceOf(TelegramApiClient::class, $this->app->make(TelegramApiClient::class));
        $this->assertNotNull($this->app->make('telegram.api'));
        $this->assertNotNull($this->app->make('telegram.router'));
    }

    public function test_telegram_fake_supports_target_api_for_outgoing_and_incoming_assertions(): void
    {
        $fake = Telegram::fake();
        $fake->respond('sendMessage', ['message_id' => 10]);

        Telegram::sendMessage(2001, 'Welcome!');
        $fake->receive(self::fakeMessage('/start'));
        $fake->receive(self::fakeCallbackQuery('profile:42'));

        $fake->assertMessageSent('Welcome!');
        $fake->assertCommandReceived('start');
        $fake->assertCallbackReceived('profile:42');
        $fake->assertApiCalledWith('sendMessage', [2001, 'Welcome!']);
    }

    public function test_fake_update_payloads_cover_message_command_and_callback_query_shapes(): void
    {
        $message = self::fakeMessage('/start');
        $callback = self::fakeCallbackQuery('profile:42');

        $this->assertSame('/start', $message['message']['text']);
        $this->assertSame('profile:42', $callback['callback_query']['data']);
        $this->assertSame(1001, self::fakeUser()['id']);
        $this->assertSame(2001, self::fakeChat()['id']);
        $this->assertSame('/start', TelegramUpdate::fromArray($message)->message->text);
    }

    public function test_events_can_be_asserted_for_all_core_update_types(): void
    {
        Event::fake();
        $message = TelegramUpdate::fromArray(self::fakeMessage('/start'));
        $callback = TelegramUpdate::fromArray(self::fakeCallbackQuery('profile'));

        event(new UpdateReceived($message));
        event(new MessageReceived($message));
        event(new CommandReceived($message));
        event(new CallbackQueryReceived($callback));
        event(new RouteMatched($message, ['type' => 'command', 'pattern' => '/start']));

        Event::assertDispatched(UpdateReceived::class);
        Event::assertDispatched(MessageReceived::class);
        Event::assertDispatched(CommandReceived::class);
        Event::assertDispatched(CallbackQueryReceived::class);
        Event::assertDispatched(RouteMatched::class);
    }

    public function test_queue_fake_can_assert_update_processing_job_is_dispatched(): void
    {
        Bus::fake();
        $job = new ProcessTelegramUpdateJob(self::fakeMessage('/start'), [
            'type' => 'command',
            'pattern' => '/start',
            'callback' => [TelegramTestController::class, 'start'],
        ]);

        dispatch($job);

        Bus::assertDispatched(ProcessTelegramUpdateJob::class);
    }

    public function test_keyboard_assertions_can_inspect_callbacks_and_markup(): void
    {
        $keyboard = Keyboard::inline()
            ->button('Profile', 'profile:42')
            ->row()
            ->url('Docs', 'https://example.com');

        $markup = $keyboard->toArray();

        $this->assertSame('profile:42', $markup['inline_keyboard'][0][0]['callback_data']);
        $this->assertSame('https://example.com', $markup['inline_keyboard'][1][0]['url']);
    }

    public function test_api_registry_exposes_every_registered_method_for_contract_tests(): void
    {
        $methods = TelegramApiMethodRegistry::methods();

        $this->assertNotEmpty($methods);
        foreach ($methods as $method) {
            $definition = TelegramApiMethodRegistry::parameters($method);
            $this->assertSame(
                [...$definition['required'], ...$definition['optional']],
                TelegramApiMethodRegistry::parameterNames($method)
            );
        }
    }

    public function test_route_builders_register_commands_text_callbacks_and_update_types(): void
    {
        TelegramBot::onCommand('start', [TelegramTestController::class, 'start'])->name('start');
        TelegramBot::onText('hello', [TelegramTestController::class, 'start'])->name('hello');
        TelegramBot::onCallbackQuery('profile:{id}', [TelegramTestController::class, 'start'])->name('profile');
        TelegramBot::onInlineQuery(null, [TelegramTestController::class, 'start'])->name('inline');
        TelegramBot::onEditedMessage(null, [TelegramTestController::class, 'start'])->name('edited');

        $routes = TelegramBot::getRoutes();

        $this->assertCount(5, $routes);
        $this->assertSame('/start', TelegramBot::getRouteByName('start')['pattern']);
        $this->assertSame('profile:{id}', TelegramBot::getRouteByName('profile')['pattern']);
    }

    /** @return array<string, mixed> */
    private static function fakeUser(): array
    {
        return ['id' => 1001, 'is_bot' => false, 'first_name' => 'Test', 'username' => 'test_user'];
    }

    /** @return array<string, mixed> */
    private static function fakeChat(): array
    {
        return ['id' => 2001, 'type' => 'private', 'first_name' => 'Test'];
    }

    /** @return array<string, mixed> */
    private static function fakeMessage(string $text): array
    {
        return [
            'update_id' => 3001,
            'message' => [
                'message_id' => 4001,
                'from' => self::fakeUser(),
                'chat' => self::fakeChat(),
                'date' => 1700000000,
                'text' => $text,
            ],
        ];
    }

    /** @return array<string, mixed> */
    private static function fakeCallbackQuery(string $data): array
    {
        return [
            'update_id' => 3002,
            'callback_query' => [
                'id' => 'callback-test-1',
                'from' => self::fakeUser(),
                'message' => self::fakeMessage('button')['message'],
                'chat_instance' => 'test-chat-instance',
                'data' => $data,
            ],
        ];
    }
}
