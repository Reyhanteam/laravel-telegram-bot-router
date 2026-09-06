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

    public function test_telegram_fake_supports_api_responses_incoming_factories_and_assertions(): void
    {
        $fake = Telegram::fake();
        $fake->respond('getMe', ['id' => 99, 'is_bot' => true, 'username' => 'test_bot']);
        $fake->respond('sendMessage', ['message_id' => 10]);

        $this->assertSame(['id' => 99, 'is_bot' => true, 'username' => 'test_bot'], Telegram::getMe());
        Telegram::sendMessage(2001, 'Welcome!');

        $fake->command('/start');
        $fake->message('hello');
        $fake->callbackQuery('profile:42');

        $fake->assertMessageSent('Welcome!');
        $fake->assertCommandReceived('start');
        $fake->assertMessageReceived('hello');
        $fake->assertCallbackReceived('profile:42');
        $fake->assertApiCalledWith('sendMessage', [2001, 'Welcome!']);
        $fake->assertNoApiCall('deleteMessage');
    }

    public function test_fake_update_payloads_cover_message_user_chat_and_callback_shapes(): void
    {
        $fake = Telegram::fake();
        $message = $fake->command('start');
        $callback = $fake->callbackQuery('profile:42');

        $this->assertSame('/start', $message->message->text);
        $this->assertSame('profile:42', $callback->callbackQueryData());
        $this->assertSame(1001, $message->userId());
        $this->assertSame(2001, $message->chatId());
        $this->assertSame(4001, $message->messageId());
        $this->assertCount(2, $fake->updates());
    }

    public function test_events_can_be_asserted_for_core_message_and_callback_events(): void
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

    public function test_keyboard_markup_can_be_asserted_through_the_fake(): void
    {
        $fake = Telegram::fake();
        $keyboard = Keyboard::inline()
            ->button('Profile', 'profile:42')
            ->row()
            ->url('Docs', 'https://example.com');

        $markup = $keyboard->toArray();
        Telegram::sendMessage(2001, 'Choose:', null, null, null, null, null, null, null, null, null, null, null, null, $markup);

        $fake->assertMessageSent('Choose:');
        $fake->assertKeyboardSent($markup);
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

    public function test_webhook_http_request_routes_command_to_controller_and_fake_api(): void
    {
        $fake = Telegram::fake();
        $fake->respond('sendMessage', ['message_id' => 10]);
        TelegramTestController::$executed = false;

        TelegramBot::onCommand('start', [TelegramTestController::class, 'start']);

        $response = $this->postJson('/telegram/webhook', self::fakeMessage('/start'));

        $response->assertOk();
        $response->assertJson(['status' => 'ok']);
        $this->assertTrue(TelegramTestController::$executed);
        $fake->assertCommandReceived('start');
        $fake->assertMessageSent('Welcome!');
        $fake->assertApiCalledWith('sendMessage', [2001, 'Welcome!']);
    }

    public function test_webhook_http_request_dispatches_core_events_for_command(): void
    {
        Event::fake();
        TelegramBot::onCommand('start', [TelegramTestController::class, 'start']);

        $response = $this->postJson('/telegram/webhook', self::fakeMessage('/start'));

        $response->assertOk();
        Event::assertDispatched(UpdateReceived::class);
        Event::assertDispatched(MessageReceived::class);
        Event::assertDispatched(CommandReceived::class);
        Event::assertDispatched(RouteMatched::class);
    }

    public function test_webhook_rejects_invalid_update_without_calling_telegram_api(): void
    {
        $fake = Telegram::fake();

        $response = $this->post('/telegram/webhook', ['invalid' => 'payload']);

        $response->assertStatus(400);
        $fake->assertNoApiCall('sendMessage');
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
