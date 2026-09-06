<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;

final class KeyboardTest extends TestCase
{
    public function test_inline_keyboard_builds_multiple_rows_and_button_types(): void
    {
        $keyboard = Keyboard::inline()
            ->button('پروفایل', 'profile')
            ->button('تنظیمات', 'settings')
            ->row()
            ->url('وب‌سایت', 'https://example.com')
            ->webApp('اپ', 'https://example.com/app')
            ->login('ورود', 'https://example.com/login')
            ->switchInlineQuery('جستجو', 'reyhan')
            ->switchInlineQueryCurrentChat('در چت', 'test');

        $this->assertSame([
            'inline_keyboard' => [
                [
                    ['text' => 'پروفایل', 'callback_data' => 'profile'],
                    ['text' => 'تنظیمات', 'callback_data' => 'settings'],
                ],
                [
                    ['text' => 'وب‌سایت', 'url' => 'https://example.com'],
                    ['text' => 'اپ', 'web_app' => ['url' => 'https://example.com/app']],
                    ['text' => 'ورود', 'login_url' => ['url' => 'https://example.com/login']],
                    ['text' => 'جستجو', 'switch_inline_query' => 'reyhan'],
                    ['text' => 'در چت', 'switch_inline_query_current_chat' => 'test'],
                ],
            ],
        ], $keyboard->toArray());
    }

    public function test_button_detects_url_and_callback_data(): void
    {
        $keyboard = Keyboard::inline()
            ->button('پروفایل', 'profile')
            ->button('وب‌سایت', 'https://example.com');

        $this->assertSame('profile', $keyboard->toArray()['inline_keyboard'][0][0]['callback_data']);
        $this->assertSame('https://example.com', $keyboard->toArray()['inline_keyboard'][0][1]['url']);
    }

    public function test_reply_keyboard_supports_options(): void
    {
        $keyboard = Keyboard::reply()
            ->button('بله')
            ->button('خیر')
            ->row()
            ->button('بعداً')
            ->resize()
            ->oneTime()
            ->persistent()
            ->selective()
            ->placeholder('انتخاب کنید');

        $this->assertSame([
            'keyboard' => [
                [
                    ['text' => 'بله'],
                    ['text' => 'خیر'],
                ],
                [
                    ['text' => 'بعداً'],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'is_persistent' => true,
            'selective' => true,
            'input_field_placeholder' => 'انتخاب کنید',
        ], $keyboard->toArray());
    }

    public function test_conditional_buttons_are_dynamic(): void
    {
        $keyboard = Keyboard::inline()
            ->button('ثابت', 'fixed')
            ->when(true, fn (Keyboard $keyboard) => $keyboard->button('نمایش', 'shown'))
            ->when(false, fn (Keyboard $keyboard) => $keyboard->button('مخفی', 'hidden'));

        $buttons = $keyboard->toArray()['inline_keyboard'][0];

        $this->assertCount(2, $buttons);
        $this->assertSame('shown', $buttons[1]['callback_data']);
    }

    public function test_callback_data_helper_builds_route_data(): void
    {
        $this->assertSame('profile?id=42&tab=settings', Keyboard::callbackData('profile', [
            'id' => 42,
            'tab' => 'settings',
        ]));
    }

    public function test_callback_data_is_limited_to_64_bytes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Keyboard::inline()->callbackButton('Too long', str_repeat('x', 65));
    }

    public function test_empty_keyboard_fails_validation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Keyboard::inline()->validate();
    }

    public function test_remove_and_force_reply_are_valid_reply_markups(): void
    {
        $this->assertSame(['remove_keyboard' => true], Keyboard::reply()->remove()->toArray());
        $this->assertSame(['force_reply' => true], Keyboard::reply()->forceReply()->toArray());
    }
}
