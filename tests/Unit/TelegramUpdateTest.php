<?php

namespace Tests\Unit;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use Tests\TestCase;

class TelegramUpdateTest extends TestCase
{
    public function test_contact_message_without_text_returns_null_text(): void
    {
        $update = new TelegramUpdate([
            'update_id' => 1001,
            'message' => [
                'message_id' => 10,
                'chat' => ['id' => 123, 'type' => 'private'],
                'from' => ['id' => 456, 'is_bot' => false],
                'contact' => [
                    'phone_number' => '+491701234567',
                    'first_name' => 'Hossein',
                    'user_id' => 456,
                ],
            ],
        ]);

        $this->assertNull($update->text());
    }

    public function test_text_message_still_returns_message_text(): void
    {
        $update = new TelegramUpdate([
            'update_id' => 1002,
            'message' => [
                'message_id' => 11,
                'chat' => ['id' => 123, 'type' => 'private'],
                'from' => ['id' => 456, 'is_bot' => false],
                'text' => 'hello',
            ],
        ]);

        $this->assertSame('hello', $update->text());
    }
}
