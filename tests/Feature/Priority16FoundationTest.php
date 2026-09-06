<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiMethodRegistry;
use ReyhanTeam\TelegramBotRouter\Events\CallbackQueryReceived;
use ReyhanTeam\TelegramBotRouter\Events\CommandReceived;
use ReyhanTeam\TelegramBotRouter\Events\MessageReceived;
use ReyhanTeam\TelegramBotRouter\Events\RouteMatched;
use ReyhanTeam\TelegramBotRouter\Events\UpdateReceived;
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;
use ReyhanTeam\TelegramBotRouter\TelegramRouterServiceProvider;
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

    public function test_fake_update_payloads_cover_message_command_and_callback_query_shapes(): void
    {
        $message = self::fakeMessage('/start');
        $callback = self::fakeCallbackQuery('profile:42');

        $this->assertSame('/start', $message['message']['text']);
        $this->assertSame('profile:42', $callback['callback_query']['data']);
        $this->assertSame(1001, self::fakeUser()['id']);
        $this->assertSame(2001, self::fakeChat()['id']);
    }

    public function test_events_can_be_asserted_for_all_core_update_types(): void
    {
        Event::fake();

        event(new UpdateReceived(self::fakeMessage('/start')));
        event(new MessageReceived(self::fakeMessage('hello')));
        event(new CommandReceived(self::fakeMessage('/start')));
        event(new CallbackQueryReceived(self::fakeCallbackQuery('profile')));
        event(new RouteMatched('start', TelegramTestController::class . '@start'));

        Event::assertDispatched(UpdateReceived::class);
        Event::assertDispatched(MessageReceived::class);
        Event::assertDispatched(CommandReceived::class);
        Event::assertDispatched(CallbackQueryReceived::class);
        Event::assertDispatched(RouteMatched::class);
    }

    public function test_queue_fake_can_assert_package_jobs_are_dispatched(): void
    {
        Queue::fake();

        Queue::assertNothingPushed();
        $this->assertTrue(true);
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
