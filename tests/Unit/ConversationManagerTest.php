<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Conversation\ConversationManager;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ConversationManagerTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('cache.default', 'array');
    }

    public function test_conversation_runs_steps_in_order_and_stores_data(): void
    {
        $manager = app(ConversationManager::class);
        $update = $this->update(10, 20, 'Hossein');
        $called = [];

        $manager->start($update, 'register', [
            function ($update, $data) use (&$called) {
                $called[] = 'name';
                return ['data' => ['name' => $update->text()]];
            },
            function ($update, $data) use (&$called) {
                $called[] = 'phone';
                return ['data' => $data + ['phone' => $update->text()]];
            },
        ]);

        $manager->handle($update);
        $this->assertTrue($manager->active($update));
        $this->assertSame(['name' => 'Hossein'], $manager->data($update));

        $update = $this->update(10, 20, '09120000000');
        $manager->handle($update);

        $this->assertSame(['name', 'phone'], $called);
        $this->assertFalse($manager->active($update));
    }

    public function test_done_removes_conversation_state(): void
    {
        $manager = app(ConversationManager::class);
        $update = $this->update(30, 40, 'done');

        $manager->start($update, 'test', [
            fn () => ['done' => true],
        ]);

        $this->assertTrue($manager->handle($update));
        $this->assertFalse($manager->active($update));
    }

    public function test_conversations_are_isolated_by_chat_and_user(): void
    {
        $manager = app(ConversationManager::class);
        $first = $this->update(1, 100, 'one');
        $second = $this->update(2, 200, 'two');

        $manager->start($first, 'one', [
            fn ($update) => ['data' => ['value' => $update->text()]],
        ]);
        $manager->start($second, 'two', [
            fn ($update) => ['data' => ['value' => $update->text()]],
        ]);

        $manager->handle($first);
        $manager->handle($second);

        $this->assertSame(['value' => 'one'], $manager->data($first));
        $this->assertSame(['value' => 'two'], $manager->data($second));
    }

    public function test_missing_conversation_returns_false(): void
    {
        $manager = app(ConversationManager::class);
        $update = $this->update(50, 60, 'hello');

        $this->assertFalse($manager->handle($update));
    }

    private function update(int $chatId, int $userId, string $text): TelegramUpdate
    {
        return new TelegramUpdate([
            'update_id' => random_int(1, 999999),
            'message' => [
                'message_id' => random_int(1, 999999),
                'chat' => ['id' => $chatId, 'type' => 'private'],
                'from' => ['id' => $userId, 'is_bot' => false],
                'text' => $text,
            ],
        ]);
    }
}
