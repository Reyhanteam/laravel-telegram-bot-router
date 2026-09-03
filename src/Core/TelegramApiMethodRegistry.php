<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Core;

/**
 * Registry for supported Telegram Bot API methods.
 *
 * Adding a new Telegram method only requires adding its name here. The HTTP
 * transport and the rest of the package do not need to change.
 */
final class TelegramApiMethodRegistry
{
    /**
     * @return list<string>
     */
    public static function methods(): array
    {
        return [
            'getMe',
            'logOut',
            'close',
            'sendMessage',
            'sendMessageDraft',
            'sendPhoto',
            'sendAudio',
            'sendDocument',
            'sendVideo',
            'sendAnimation',
            'sendVoice',
            'sendVideoNote',
            'sendPaidMedia',
            'sendMediaGroup',
            'sendLivePhoto',
            'sendContact',
            'sendLocation',
            'sendVenue',
            'sendPoll',
            'sendDice',
            'sendChatAction',
            'sendGift',
            'sendGame',
            'sendInvoice',
            'sendRichMessage',
            'sendRichMessageDraft',
            'getUserProfilePhotos',
            'getUserProfileAudios',
            'setUserEmojiStatus',
            'getFile',
            'banChatMember',
            'unbanChatMember',
            'restrictChatMember',
            'promoteChatMember',
            'setChatAdministratorCustomTitle',
            'banChatSenderChat',
            'unbanChatSenderChat',
            'setChatPermissions',
            'exportChatInviteLink',
            'createChatInviteLink',
            'editChatInviteLink',
            'revokeChatInviteLink',
            'approveChatJoinRequest',
            'declineChatJoinRequest',
            'answerChatJoinRequestQuery',
            'sendChatJoinRequestWebApp',
            'setChatPhoto',
            'deleteChatPhoto',
            'setChatTitle',
            'setChatDescription',
        ];
    }

    public static function supports(string $method): bool
    {
        return in_array($method, self::methods(), true);
    }
}
