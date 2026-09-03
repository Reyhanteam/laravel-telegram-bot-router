<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

/**
 * Developer-friendly Telegram Bot API facade.
 *
 * Every supported Telegram API method is declared explicitly so IDEs can
 * provide real parameter suggestions, required/optional information,
 * autocomplete and hover documentation. Required parameters come first.
 *
 * @see https://core.telegram.org/bots/api
 */
final class BOT extends Facade
{
    public static function getMe(): mixed
    {
        return static::getFacadeRoot()->getMe();
    }

    public static function logOut(): mixed
    {
        return static::getFacadeRoot()->logOut();
    }

    public static function close(): mixed
    {
        return static::getFacadeRoot()->close();
    }

    public static function sendMessage(
        int|string $chatId,
        string $text,
        ?string $businessConnectionId = null,
        ?int $messageThreadId = null,
        ?string $directMessagesTopicId = null,
        ?string $parseMode = null,
        ?array $entities = null,
        ?array $linkPreviewOptions = null,
        ?bool $disableNotification = null,
        ?bool $protectContent = null,
        ?bool $allowPaidBroadcast = null,
        mixed $messageEffectId = null,
        ?array $suggestedPostParameters = null,
        ?array $replyParameters = null,
        ?array $replyMarkup = null,
        ?array $ephemeralMessageParameters = null,
    ): mixed {
        return static::getFacadeRoot()->sendMessage(
            $chatId, $text, $businessConnectionId, $messageThreadId,
            $directMessagesTopicId, $parseMode, $entities, $linkPreviewOptions,
            $disableNotification, $protectContent, $allowPaidBroadcast,
            $messageEffectId, $suggestedPostParameters, $replyParameters,
            $replyMarkup, $ephemeralMessageParameters,
        );
    }

    public static function sendMessageDraft(
        int|string $chatId,
        int $draftId,
        string $text,
        ?int $messageThreadId = null,
        ?string $parseMode = null,
        ?array $entities = null,
        ?array $linkPreviewOptions = null,
        ?bool $disableNotification = null,
        ?bool $protectContent = null,
        ?bool $allowPaidBroadcast = null,
        mixed $messageEffectId = null,
        ?array $suggestedPostParameters = null,
        ?array $replyParameters = null,
        ?array $replyMarkup = null,
        ?bool $canStop = null,
        ?bool $keepOnStop = null,
    ): mixed {
        return static::getFacadeRoot()->sendMessageDraft(
            $chatId, $draftId, $text, $messageThreadId, $parseMode, $entities,
            $linkPreviewOptions, $disableNotification, $protectContent,
            $allowPaidBroadcast, $messageEffectId, $suggestedPostParameters,
            $replyParameters, $replyMarkup, $canStop, $keepOnStop,
        );
    }

    public static function sendPhoto(int|string $chatId, string $photo, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendPhoto($chatId, $photo, ...$optional);
    }

    public static function sendAudio(int|string $chatId, string $audio, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendAudio($chatId, $audio, ...$optional);
    }

    public static function sendDocument(int|string $chatId, string $document, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendDocument($chatId, $document, ...$optional);
    }

    public static function sendVideo(int|string $chatId, string $video, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendVideo($chatId, $video, ...$optional);
    }

    public static function sendAnimation(int|string $chatId, string $animation, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendAnimation($chatId, $animation, ...$optional);
    }

    public static function sendVoice(int|string $chatId, string $voice, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendVoice($chatId, $voice, ...$optional);
    }

    public static function sendVideoNote(int|string $chatId, string $videoNote, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendVideoNote($chatId, $videoNote, ...$optional);
    }

    public static function sendPaidMedia(int|string $chatId, int $starCount, array $media, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendPaidMedia($chatId, $starCount, $media, ...$optional);
    }

    public static function sendMediaGroup(int|string $chatId, array $media, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendMediaGroup($chatId, $media, ...$optional);
    }

    public static function sendLivePhoto(int|string $chatId, string $livePhoto, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendLivePhoto($chatId, $livePhoto, ...$optional);
    }

    public static function sendContact(int|string $chatId, string $phoneNumber, string $firstName, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendContact($chatId, $phoneNumber, $firstName, ...$optional);
    }

    public static function sendLocation(int|string $chatId, float $latitude, float $longitude, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendLocation($chatId, $latitude, $longitude, ...$optional);
    }

    public static function sendVenue(int|string $chatId, float $latitude, float $longitude, string $title, string $address, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendVenue($chatId, $latitude, $longitude, $title, $address, ...$optional);
    }

    public static function sendPoll(int|string $chatId, string $question, array $options, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendPoll($chatId, $question, $options, ...$optional);
    }

    public static function sendDice(int|string $chatId, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendDice($chatId, ...$optional);
    }

    public static function sendChatAction(int|string $chatId, string $action, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendChatAction($chatId, $action, ...$optional);
    }

    public static function sendGift(string $giftId, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendGift($giftId, ...$optional);
    }

    public static function sendGame(int|string $chatId, string $gameShortName, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendGame($chatId, $gameShortName, ...$optional);
    }

    public static function sendInvoice(int|string $chatId, string $title, string $description, string $payload, string $currency, array $prices, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendInvoice($chatId, $title, $description, $payload, $currency, $prices, ...$optional);
    }

    public static function sendRichMessage(int|string $chatId, array $richMessage, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendRichMessage($chatId, $richMessage, ...$optional);
    }

    public static function sendRichMessageDraft(int|string $chatId, int $draftId, array $richMessage, mixed ...$optional): mixed
    {
        return static::getFacadeRoot()->sendRichMessageDraft($chatId, $draftId, $richMessage, ...$optional);
    }

    public static function getUserProfilePhotos(int|string $userId, mixed ...$optional): mixed { return static::getFacadeRoot()->getUserProfilePhotos($userId, ...$optional); }
    public static function getUserProfileAudios(int|string $userId, mixed ...$optional): mixed { return static::getFacadeRoot()->getUserProfileAudios($userId, ...$optional); }
    public static function setUserEmojiStatus(int|string $userId, mixed ...$optional): mixed { return static::getFacadeRoot()->setUserEmojiStatus($userId, ...$optional); }
    public static function getFile(string $fileId, mixed ...$optional): mixed { return static::getFacadeRoot()->getFile($fileId, ...$optional); }
    public static function banChatMember(int|string $chatId, int|string $userId, mixed ...$optional): mixed { return static::getFacadeRoot()->banChatMember($chatId, $userId, ...$optional); }
    public static function unbanChatMember(int|string $chatId, int|string $userId, mixed ...$optional): mixed { return static::getFacadeRoot()->unbanChatMember($chatId, $userId, ...$optional); }
    public static function restrictChatMember(int|string $chatId, int|string $userId, array $permissions, mixed ...$optional): mixed { return static::getFacadeRoot()->restrictChatMember($chatId, $userId, $permissions, ...$optional); }
    public static function promoteChatMember(int|string $chatId, int|string $userId, mixed ...$optional): mixed { return static::getFacadeRoot()->promoteChatMember($chatId, $userId, ...$optional); }
    public static function setChatAdministratorCustomTitle(int|string $chatId, int|string $userId, string $customTitle, mixed ...$optional): mixed { return static::getFacadeRoot()->setChatAdministratorCustomTitle($chatId, $userId, $customTitle, ...$optional); }
    public static function banChatSenderChat(int|string $chatId, int|string $senderChatId, mixed ...$optional): mixed { return static::getFacadeRoot()->banChatSenderChat($chatId, $senderChatId, ...$optional); }
    public static function unbanChatSenderChat(int|string $chatId, int|string $senderChatId, mixed ...$optional): mixed { return static::getFacadeRoot()->unbanChatSenderChat($chatId, $senderChatId, ...$optional); }
    public static function setChatPermissions(int|string $chatId, array $permissions, mixed ...$optional): mixed { return static::getFacadeRoot()->setChatPermissions($chatId, $permissions, ...$optional); }
    public static function exportChatInviteLink(int|string $chatId): mixed { return static::getFacadeRoot()->exportChatInviteLink($chatId); }
    public static function createChatInviteLink(int|string $chatId, mixed ...$optional): mixed { return static::getFacadeRoot()->createChatInviteLink($chatId, ...$optional); }
    public static function editChatInviteLink(int|string $chatId, string $inviteLink, mixed ...$optional): mixed { return static::getFacadeRoot()->editChatInviteLink($chatId, $inviteLink, ...$optional); }
    public static function revokeChatInviteLink(int|string $chatId, string $inviteLink): mixed { return static::getFacadeRoot()->revokeChatInviteLink($chatId, $inviteLink); }
    public static function approveChatJoinRequest(int|string $chatId, int|string $userId): mixed { return static::getFacadeRoot()->approveChatJoinRequest($chatId, $userId); }
    public static function declineChatJoinRequest(int|string $chatId, int|string $userId): mixed { return static::getFacadeRoot()->declineChatJoinRequest($chatId, $userId); }
    public static function answerChatJoinRequestQuery(string $queryId, string $result): mixed { return static::getFacadeRoot()->answerChatJoinRequestQuery($queryId, $result); }
    public static function sendChatJoinRequestWebApp(string $chatJoinRequestQueryId, string $webAppUrl): mixed { return static::getFacadeRoot()->sendChatJoinRequestWebApp($chatJoinRequestQueryId, $webAppUrl); }
    public static function setChatPhoto(int|string $chatId, string $photo): mixed { return static::getFacadeRoot()->setChatPhoto($chatId, $photo); }
    public static function deleteChatPhoto(int|string $chatId): mixed { return static::getFacadeRoot()->deleteChatPhoto($chatId); }
    public static function setChatTitle(int|string $chatId, string $title): mixed { return static::getFacadeRoot()->setChatTitle($chatId, $title); }
    public static function setChatDescription(int|string $chatId, string $description): mixed { return static::getFacadeRoot()->setChatDescription($chatId, $description); }

    public static function pinChatMessage(
        int|string $chatId,
        int $messageId,
        ?string $businessConnectionId = null,
        ?bool $disableNotification = null,
    ): mixed {
        return static::getFacadeRoot()->pinChatMessage($chatId, $messageId, $businessConnectionId, $disableNotification);
    }

    public static function unpinChatMessage(
        int|string $chatId,
        ?string $businessConnectionId = null,
        ?int $messageId = null,
    ): mixed {
        return static::getFacadeRoot()->unpinChatMessage($chatId, $businessConnectionId, $messageId);
    }

    public static function unpinAllChatMessages(int|string $chatId): mixed { return static::getFacadeRoot()->unpinAllChatMessages($chatId); }
    public static function leaveChat(int|string $chatId): mixed { return static::getFacadeRoot()->leaveChat($chatId); }
    public static function getChat(int|string $chatId): mixed { return static::getFacadeRoot()->getChat($chatId); }
    public static function getChatAdministrators(int|string $chatId, ?bool $returnBots = null): mixed { return static::getFacadeRoot()->getChatAdministrators($chatId, $returnBots); }
    public static function getChatMemberCount(int|string $chatId): mixed { return static::getFacadeRoot()->getChatMemberCount($chatId); }
    public static function getChatMember(int|string $chatId, int|string $userId): mixed { return static::getFacadeRoot()->getChatMember($chatId, $userId); }
    public static function getUserPersonalChatMessages(int|string $userId, int $limit): mixed { return static::getFacadeRoot()->getUserPersonalChatMessages($userId, $limit); }
    public static function setChatStickerSet(int|string $chatId, string $stickerSetName): mixed { return static::getFacadeRoot()->setChatStickerSet($chatId, $stickerSetName); }
    public static function deleteChatStickerSet(int|string $chatId): mixed { return static::getFacadeRoot()->deleteChatStickerSet($chatId); }
    public static function getForumTopicIconStickers(): mixed { return static::getFacadeRoot()->getForumTopicIconStickers(); }
    public static function createForumTopic(int|string $chatId, string $name, ?int $iconColor = null, ?string $iconCustomEmojiId = null): mixed { return static::getFacadeRoot()->createForumTopic($chatId, $name, $iconColor, $iconCustomEmojiId); }
    public static function editForumTopic(int|string $chatId, int $messageThreadId, ?string $name = null, ?string $iconCustomEmojiId = null): mixed { return static::getFacadeRoot()->editForumTopic($chatId, $messageThreadId, $name, $iconCustomEmojiId); }
    public static function closeForumTopic(int|string $chatId, int $messageThreadId): mixed { return static::getFacadeRoot()->closeForumTopic($chatId, $messageThreadId); }
    public static function reopenForumTopic(int|string $chatId, int $messageThreadId): mixed { return static::getFacadeRoot()->reopenForumTopic($chatId, $messageThreadId); }
    public static function deleteForumTopic(int|string $chatId, int $messageThreadId): mixed { return static::getFacadeRoot()->deleteForumTopic($chatId, $messageThreadId); }
    public static function unpinAllForumTopicMessages(int|string $chatId, int $messageThreadId): mixed { return static::getFacadeRoot()->unpinAllForumTopicMessages($chatId, $messageThreadId); }
    public static function editGeneralForumTopic(int|string $chatId, string $name): mixed { return static::getFacadeRoot()->editGeneralForumTopic($chatId, $name); }
    public static function closeGeneralForumTopic(int|string $chatId): mixed { return static::getFacadeRoot()->closeGeneralForumTopic($chatId); }
    public static function reopenGeneralForumTopic(int|string $chatId): mixed { return static::getFacadeRoot()->reopenGeneralForumTopic($chatId); }
    public static function hideGeneralForumTopic(int|string $chatId): mixed { return static::getFacadeRoot()->hideGeneralForumTopic($chatId); }
    public static function unhideGeneralForumTopic(int|string $chatId): mixed { return static::getFacadeRoot()->unhideGeneralForumTopic($chatId); }
    public static function unpinAllGeneralForumTopicMessages(int|string $chatId): mixed { return static::getFacadeRoot()->unpinAllGeneralForumTopicMessages($chatId); }
    public static function answerCallbackQuery(string $callbackQueryId, mixed ...$optional): mixed { return static::getFacadeRoot()->answerCallbackQuery($callbackQueryId, ...$optional); }
    public static function answerGuestQuery(string $guestQueryId, string $result): mixed { return static::getFacadeRoot()->answerGuestQuery($guestQueryId, $result); }
    public static function getUserChatBoosts(int|string $chatId, int|string $userId): mixed { return static::getFacadeRoot()->getUserChatBoosts($chatId, $userId); }
    public static function getBusinessConnection(string $businessConnectionId): mixed { return static::getFacadeRoot()->getBusinessConnection($businessConnectionId); }
    public static function getManagedBotToken(int|string $userId): mixed { return static::getFacadeRoot()->getManagedBotToken($userId); }
    public static function replaceManagedBotToken(int|string $userId): mixed { return static::getFacadeRoot()->replaceManagedBotToken($userId); }
    public static function getManagedBotAccessSettings(int|string $userId): mixed { return static::getFacadeRoot()->getManagedBotAccessSettings($userId); }
    public static function setManagedBotAccessSettings(int|string $userId, bool $isAccessRestricted, ?array $addedUserIds = null): mixed { return static::getFacadeRoot()->setManagedBotAccessSettings($userId, $isAccessRestricted, $addedUserIds); }
    public static function setMyCommands(array $commands, ?array $scope = null, ?string $languageCode = null): mixed { return static::getFacadeRoot()->setMyCommands($commands, $scope, $languageCode); }
    public static function deleteMyCommands(?array $scope = null, ?string $languageCode = null): mixed { return static::getFacadeRoot()->deleteMyCommands($scope, $languageCode); }
    public static function getMyCommands(?array $scope = null, ?string $languageCode = null): mixed { return static::getFacadeRoot()->getMyCommands($scope, $languageCode); }
    public static function setMyName(?string $name = null, ?string $languageCode = null): mixed { return static::getFacadeRoot()->setMyName($name, $languageCode); }
    public static function getMyName(?string $languageCode = null): mixed { return static::getFacadeRoot()->getMyName($languageCode); }
    public static function setMyDescription(?string $description = null, ?string $languageCode = null): mixed { return static::getFacadeRoot()->setMyDescription($description, $languageCode); }
    public static function getMyDescription(?string $languageCode = null): mixed { return static::getFacadeRoot()->getMyDescription($languageCode); }
    public static function setMyShortDescription(?string $shortDescription = null, ?string $languageCode = null): mixed { return static::getFacadeRoot()->setMyShortDescription($shortDescription, $languageCode); }
    public static function getMyShortDescription(?string $languageCode = null): mixed { return static::getFacadeRoot()->getMyShortDescription($languageCode); }
    public static function setMyProfilePhoto(string $photo): mixed { return static::getFacadeRoot()->setMyProfilePhoto($photo); }
    public static function removeMyProfilePhoto(): mixed { return static::getFacadeRoot()->removeMyProfilePhoto(); }
    public static function setChatMenuButton(?int $chatId = null, ?array $menuButton = null): mixed { return static::getFacadeRoot()->setChatMenuButton($chatId, $menuButton); }
    public static function getChatMenuButton(?int $chatId = null): mixed { return static::getFacadeRoot()->getChatMenuButton($chatId); }
    public static function setMyDefaultAdministratorRights(?array $rights = null, ?bool $forChannels = null): mixed { return static::getFacadeRoot()->setMyDefaultAdministratorRights($rights, $forChannels); }
    public static function getMyDefaultAdministratorRights(?bool $forChannels = null): mixed { return static::getFacadeRoot()->getMyDefaultAdministratorRights($forChannels); }
    public static function getAvailableGifts(): mixed { return static::getFacadeRoot()->getAvailableGifts(); }
    public static function giftPremiumSubscription(int|string $userId, int $monthCount, int $starCount, ?string $text = null, ?string $textParseMode = null, ?array $textEntities = null): mixed { return static::getFacadeRoot()->giftPremiumSubscription($userId, $monthCount, $starCount, $text, $textParseMode, $textEntities); }
    public static function verifyUser(int|string $userId, ?string $customDescription = null): mixed { return static::getFacadeRoot()->verifyUser($userId, $customDescription); }

    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }
}
