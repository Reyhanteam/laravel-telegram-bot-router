<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Conversation\ConversationRegistrar;

class ConversationRegistrarTest extends TestCase
{
    public function test_step_returns_same_registrar_and_registers_steps(): void
    {
        $registrar = new ConversationRegistrar('register');
        $step = fn () => ['done' => true];

        $this->assertSame($registrar, $registrar->step($step));
    }

    public function test_ttl_returns_same_registrar(): void
    {
        $registrar = new ConversationRegistrar('register');

        $this->assertSame($registrar, $registrar->ttl(120));
    }
}
