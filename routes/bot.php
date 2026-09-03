<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\Facades\BOT;
use ReyhanTeam\TelegramBotRouter\Facades\Route;
/*
|--------------------------------------------------------------------------
| Telegram Bot Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes for your Telegram bot. These routes
| are kept separate from Laravel web routes and are handled by the package.
|
*/

Route::onCommand('start', function ($update) {
    $chatId = $update->message->chat->id;

    return BOT::sendMessage(
        $chatId,
        'سلام! برای ادامه، لطفاً شماره تلفن خود را با استفاده از دکمه زیر ارسال کنید.',
        replyMarkup: [
            'keyboard' => [
                [
                    [
                        'text' => '📱 ارسال شماره تلفن',
                        'request_contact' => true,
                    ],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ],
    );
})->privateChat();

// Telegram sends the shared phone number as a contact update.
// The contact is handled by the fallback because it is not a text command.
Route::fallback(function ($update) {
    $phoneNumber = $update->message?->contact?->phone_number ?? null;

    if ($phoneNumber === null) {
        return null;
    }

    $chatId = $update->message->chat->id;
    $userId = $update->message->from->id;

    Log::info('Telegram user shared their phone number.', [
        'user_id' => $userId,
        'chat_id' => $chatId,
        'phone_number' => $phoneNumber,
    ]);

    return BOT::sendMessage(
        $chatId,
        'ممنون! شماره تلفن شما با موفقیت دریافت و در لاگ ثبت شد. ✅',
        replyMarkup: [
            'remove_keyboard' => true,
        ],
    );
});
