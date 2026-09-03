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

            // Telegram Bot API methods 51-100 in this package's registry.
            'pinChatMessage',
            'unpinChatMessage',
            'unpinAllChatMessages',
            'leaveChat',
            'getChat',
            'getChatAdministrators',
            'getChatMemberCount',
            'getChatMember',
            'getUserPersonalChatMessages',
            'setChatStickerSet',
            'deleteChatStickerSet',
            'getForumTopicIconStickers',
            'createForumTopic',
            'editForumTopic',
            'closeForumTopic',
            'reopenForumTopic',
            'deleteForumTopic',
            'unpinAllForumTopicMessages',
            'editGeneralForumTopic',
            'closeGeneralForumTopic',
            'reopenGeneralForumTopic',
            'hideGeneralForumTopic',
            'unhideGeneralForumTopic',
            'unpinAllGeneralForumTopicMessages',
            'answerCallbackQuery',
            'answerGuestQuery',
            'getUserChatBoosts',
            'getBusinessConnection',
            'getManagedBotToken',
            'replaceManagedBotToken',
            'getManagedBotAccessSettings',
            'setManagedBotAccessSettings',
            'setMyCommands',
            'deleteMyCommands',
            'getMyCommands',
            'setMyName',
            'getMyName',
            'setMyDescription',
            'getMyDescription',
            'setMyShortDescription',
            'getMyShortDescription',
            'setMyProfilePhoto',
            'removeMyProfilePhoto',
            'setChatMenuButton',
            'getChatMenuButton',
            'setMyDefaultAdministratorRights',
            'getMyDefaultAdministratorRights',
            'getAvailableGifts',
            'giftPremiumSubscription',
            'verifyUser',
        ];
    }

    /**
     * Return the parameter names defined by Telegram for a method.
     *
     * This metadata is descriptive for now; Telegram remains the final
     * authority for validation, while the shared API client continues to
     * forward parameters without duplicating request logic per method.
     *
     * @return array{required: list<string>, optional: list<string>}
     */
    public static function parameters(string $method): array
    {
        $definitions = [
            'pinChatMessage' => [
                'required' => ['chat_id', 'message_id'],
                'optional' => ['business_connection_id', 'disable_notification'],
            ],
            'unpinChatMessage' => [
                'required' => ['chat_id'],
                'optional' => ['business_connection_id', 'message_id'],
            ],
            'unpinAllChatMessages' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'leaveChat' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'getChat' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'getChatAdministrators' => [
                'required' => ['chat_id'],
                'optional' => ['return_bots'],
            ],
            'getChatMemberCount' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'getChatMember' => [
                'required' => ['chat_id', 'user_id'],
                'optional' => [],
            ],
            'getUserPersonalChatMessages' => [
                'required' => ['user_id', 'limit'],
                'optional' => [],
            ],
            'setChatStickerSet' => [
                'required' => ['chat_id', 'sticker_set_name'],
                'optional' => [],
            ],
            'deleteChatStickerSet' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'getForumTopicIconStickers' => [
                'required' => [],
                'optional' => [],
            ],
            'createForumTopic' => [
                'required' => ['chat_id', 'name'],
                'optional' => ['icon_color', 'icon_custom_emoji_id'],
            ],
            'editForumTopic' => [
                'required' => ['chat_id', 'message_thread_id'],
                'optional' => ['name', 'icon_custom_emoji_id'],
            ],
            'closeForumTopic' => [
                'required' => ['chat_id', 'message_thread_id'],
                'optional' => [],
            ],
            'reopenForumTopic' => [
                'required' => ['chat_id', 'message_thread_id'],
                'optional' => [],
            ],
            'deleteForumTopic' => [
                'required' => ['chat_id', 'message_thread_id'],
                'optional' => [],
            ],
            'unpinAllForumTopicMessages' => [
                'required' => ['chat_id', 'message_thread_id'],
                'optional' => [],
            ],
            'editGeneralForumTopic' => [
                'required' => ['chat_id', 'name'],
                'optional' => [],
            ],
            'closeGeneralForumTopic' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'reopenGeneralForumTopic' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'hideGeneralForumTopic' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'unhideGeneralForumTopic' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'unpinAllGeneralForumTopicMessages' => [
                'required' => ['chat_id'],
                'optional' => [],
            ],
            'answerCallbackQuery' => [
                'required' => ['callback_query_id'],
                'optional' => ['text', 'show_alert', 'url', 'cache_time'],
            ],
            'answerGuestQuery' => [
                'required' => ['guest_query_id', 'result'],
                'optional' => [],
            ],
            'getUserChatBoosts' => [
                'required' => ['chat_id', 'user_id'],
                'optional' => [],
            ],
            'getBusinessConnection' => [
                'required' => ['business_connection_id'],
                'optional' => [],
            ],
            'getManagedBotToken' => [
                'required' => ['user_id'],
                'optional' => [],
            ],
            'replaceManagedBotToken' => [
                'required' => ['user_id'],
                'optional' => [],
            ],
            'getManagedBotAccessSettings' => [
                'required' => ['user_id'],
                'optional' => [],
            ],
            'setManagedBotAccessSettings' => [
                'required' => ['user_id', 'is_access_restricted'],
                'optional' => ['added_user_ids'],
            ],
            'setMyCommands' => [
                'required' => ['commands'],
                'optional' => ['scope', 'language_code'],
            ],
            'deleteMyCommands' => [
                'required' => [],
                'optional' => ['scope', 'language_code'],
            ],
            'getMyCommands' => [
                'required' => [],
                'optional' => ['scope', 'language_code'],
            ],
            'setMyName' => [
                'required' => [],
                'optional' => ['name', 'language_code'],
            ],
            'getMyName' => [
                'required' => [],
                'optional' => ['language_code'],
            ],
            'setMyDescription' => [
                'required' => [],
                'optional' => ['description', 'language_code'],
            ],
            'getMyDescription' => [
                'required' => [],
                'optional' => ['language_code'],
            ],
            'setMyShortDescription' => [
                'required' => [],
                'optional' => ['short_description', 'language_code'],
            ],
            'getMyShortDescription' => [
                'required' => [],
                'optional' => ['language_code'],
            ],
            'setMyProfilePhoto' => [
                'required' => ['photo'],
                'optional' => [],
            ],
            'removeMyProfilePhoto' => [
                'required' => [],
                'optional' => [],
            ],
            'setChatMenuButton' => [
                'required' => [],
                'optional' => ['chat_id', 'menu_button'],
            ],
            'getChatMenuButton' => [
                'required' => [],
                'optional' => ['chat_id'],
            ],
            'setMyDefaultAdministratorRights' => [
                'required' => [],
                'optional' => ['rights', 'for_channels'],
            ],
            'getMyDefaultAdministratorRights' => [
                'required' => [],
                'optional' => ['for_channels'],
            ],
            'getAvailableGifts' => [
                'required' => [],
                'optional' => [],
            ],
            'giftPremiumSubscription' => [
                'required' => ['user_id', 'month_count', 'star_count'],
                'optional' => ['text', 'text_parse_mode', 'text_entities'],
            ],
            'verifyUser' => [
                'required' => ['user_id'],
                'optional' => ['custom_description'],
            ],
        ];

        return $definitions[$method] ?? [
            'required' => [],
            'optional' => [],
        ];
    }

    public static function supports(string $method): bool
    {
        return in_array($method, self::methods(), true);
    }
}
