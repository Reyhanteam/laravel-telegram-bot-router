<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

/**
 * Developer-friendly Telegram Bot API facade.
 *
 * Each Telegram Bot API method is declared as a real static PHP method.
 * This gives PHPStorm and VS Code native parameter autocomplete, parameter
 * names, scalar types, optional defaults, and named-argument support.
 *
 * The implementation remains centralized in TelegramApiClient. The concrete
 * methods only forward their arguments to the shared client.
 *
 * Legacy associative-array calls are supported where the first parameter can
 * safely accept an array. New code should prefer the typed API.
 *
 * @see https://core.telegram.org/bots/api
 */
final class BOT extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }

    /** @return mixed */
    private static function dispatch(string $method, array $arguments): mixed
    {
        $client = static::getFacadeRoot();

        if (!$client instanceof TelegramApiClient) {
            throw new \RuntimeException('Telegram API client is not available.');
        }

        if (isset($arguments[0]) && is_array($arguments[0]) && array_keys($arguments[0]) !== range(0, count($arguments[0]) - 1)) {
            return $client->call($method, $arguments[0]);
        }

        return $client->__call($method, $arguments);
    }

    public static function getMe(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function logOut(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function close(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }

    public static function sendMessage(int|string|array $chatId, string $text = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $parseMode = null, ?array $entities = null, ?array $linkPreviewOptions = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendMessageDraft(int|string|array $chatId, int $draftId = 0, ?string $text = null, ?int $messageThreadId = null, ?string $parseMode = null, ?array $entities = null, ?array $linkPreviewOptions = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?bool $canStop = null, ?bool $keepOnStop = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendPhoto(int|string|array $chatId, string $photo = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendAudio(int|string|array $chatId, string $audio = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $duration = null, ?string $performer = null, ?string $title = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?string $thumbnail = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendDocument(int|string|array $chatId, string $document = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $thumbnail = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $disableContentTypeDetection = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendVideo(int|string|array $chatId, string $video = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $duration = null, ?int $width = null, ?int $height = null, ?string $thumbnail = null, ?string $cover = null, ?int $startTimestamp = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $supportsStreaming = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendAnimation(int|string|array $chatId, string $animation = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $duration = null, ?int $width = null, ?int $height = null, ?string $thumbnail = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $supportsStreaming = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendVoice(int|string|array $chatId, string $voice = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?int $duration = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendVideoNote(int|string|array $chatId, string $videoNote = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $duration = null, ?int $length = null, ?string $thumbnail = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendPaidMedia(int|string|array $chatId, int $starCount = 0, array $media = [], ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $payload = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendMediaGroup(int|string|array $chatId, array $media = [], ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?bool $disableNotification = null, ?bool $protectContent = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendLivePhoto(int|string|array $chatId, string $livePhoto = '', string $photo = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendContact(int|string|array $chatId, string $phoneNumber = '', string $firstName = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $lastName = null, ?string $vcard = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendLocation(int|string|array $chatId, float $latitude = 0.0, float $longitude = 0.0, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?float $horizontalAccuracy = null, ?int $livePeriod = null, ?int $heading = null, ?int $proximityAlertRadius = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendVenue(int|string|array $chatId, float $latitude = 0.0, float $longitude = 0.0, string $title = '', string $address = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $foursquareId = null, ?string $foursquareType = null, ?string $googlePlaceId = null, ?string $googlePlaceType = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendPoll(int|string|array $chatId, string $question = '', array $options = [], ?string $businessConnectionId = null, ?int $messageThreadId = null, ?string $questionParseMode = null, ?array $questionEntities = null, ?bool $isAnonymous = null, ?string $type = null, ?bool $allowsMultipleAnswers = null, ?int $correctOptionId = null, ?string $explanation = null, ?string $explanationParseMode = null, ?array $explanationEntities = null, ?int $openPeriod = null, ?int $closeDate = null, ?bool $isClosed = null, ?array $media = null, ?array $explanationMedia = null, ?bool $membersOnly = null, ?array $countryCodes = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendDice(int|string|array $chatId, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $emoji = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendChatAction(int|string|array $chatId, string $action = '', ?string $businessConnectionId = null, ?int $messageThreadId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendGift(string|array $giftId, ?int $userId = null, int|string|null $chatId = null, ?bool $payForUpgrade = null, ?string $text = null, ?string $textParseMode = null, ?array $textEntities = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendGame(int|string|array $chatId, string $gameShortName = '', ?string $businessConnectionId = null, ?int $messageThreadId = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $replyParameters = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendInvoice(int|string|array $chatId, string $title = '', string $description = '', string $payload = '', string $currency = '', array $prices = [], ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $providerToken = null, ?int $maxTipAmount = null, ?array $suggestedTipAmounts = null, ?string $startParameter = null, ?string $providerData = null, ?string $photoUrl = null, ?int $photoSize = null, ?int $photoWidth = null, ?int $photoHeight = null, ?bool $needName = null, ?bool $needPhoneNumber = null, ?bool $needEmail = null, ?bool $needShippingAddress = null, ?bool $sendPhoneNumberToProvider = null, ?bool $sendEmailToProvider = null, ?bool $isFlexible = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendRichMessage(int|string|array $chatId, array $richMessage = [], ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?array $ephemeralMessageParameters = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendRichMessageDraft(int|string|array $chatId, int $draftId = 0, array $richMessage = [], ?int $messageThreadId = null, ?array $ephemeralMessageParameters = null, ?bool $canStop = null, ?bool $keepOnStop = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }

    public static function getUserProfilePhotos(int|array $userId, ?int $offset = null, ?int $limit = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getUserProfileAudios(int|array $userId, ?int $offset = null, ?int $limit = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setUserEmojiStatus(int|array $userId, string $emojiStatusCustomEmojiId = '', ?int $emojiStatusExpirationDate = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getFile(string|array $fileId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function banChatMember(int|string|array $chatId, int $userId = 0, ?int $untilDate = null, ?bool $revokeMessages = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unbanChatMember(int|string|array $chatId, int $userId = 0, ?bool $onlyIfBanned = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function restrictChatMember(int|string|array $chatId, int $userId = 0, array $permissions = [], ?bool $useIndependentChatPermissions = null, ?int $untilDate = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function promoteChatMember(int|string|array $chatId, int $userId = 0, ?bool $isAnonymous = null, ?bool $canManageChat = null, ?bool $canDeleteMessages = null, ?bool $canManageVideoChats = null, ?bool $canRestrictMembers = null, ?bool $canPromoteMembers = null, ?bool $canChangeInfo = null, ?bool $canInviteUsers = null, ?bool $canPostStories = null, ?bool $canEditStories = null, ?bool $canPostMessages = null, ?bool $canEditMessages = null, ?bool $canPinMessages = null, ?bool $canManageTopics = null, ?bool $canManageDirectMessages = null, ?bool $canManageTags = null, ?bool $canSendWelcomeMessages = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatAdministratorCustomTitle(int|string|array $chatId, int $userId = 0, string $customTitle = ''): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function banChatSenderChat(int|string|array $chatId, int $senderChatId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unbanChatSenderChat(int|string|array $chatId, int $senderChatId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatPermissions(int|string|array $chatId, array $permissions = [], ?bool $useIndependentChatPermissions = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function exportChatInviteLink(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function createChatInviteLink(int|string|array $chatId, ?string $name = null, ?int $expireDate = null, ?int $memberLimit = null, ?bool $createsJoinRequest = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editChatInviteLink(int|string|array $chatId, string $inviteLink = '', ?string $name = null, ?int $expireDate = null, ?int $memberLimit = null, ?bool $createsJoinRequest = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function revokeChatInviteLink(int|string|array $chatId, string $inviteLink = ''): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function approveChatJoinRequest(int|string|array $chatId, int $userId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function declineChatJoinRequest(int|string|array $chatId, int $userId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function answerChatJoinRequestQuery(string $queryId, string $result): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendChatJoinRequestWebApp(string $chatJoinRequestQueryId, string $webAppUrl): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatPhoto(int|string|array $chatId, string $photo = ''): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteChatPhoto(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatTitle(int|string|array $chatId, string $title = ''): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatDescription(int|string|array $chatId, ?string $description = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function pinChatMessage(int|string|array $chatId, int $messageId = 0, ?string $businessConnectionId = null, ?bool $disableNotification = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unpinChatMessage(int|string|array $chatId, ?string $businessConnectionId = null, ?int $messageId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unpinAllChatMessages(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function leaveChat(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getChat(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getChatAdministrators(int|string|array $chatId, ?bool $returnBots = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getChatMemberCount(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getChatMember(int|string|array $chatId, int $userId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getUserPersonalChatMessages(int|array $userId, int $limit = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatStickerSet(int|string|array $chatId, string $stickerSetName = ''): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteChatStickerSet(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getForumTopicIconStickers(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function createForumTopic(int|string|array $chatId, string $name = '', ?int $iconColor = null, ?string $iconCustomEmojiId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editForumTopic(int|string|array $chatId, int $messageThreadId = 0, ?string $name = null, ?string $iconCustomEmojiId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function closeForumTopic(int|string|array $chatId, int $messageThreadId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function reopenForumTopic(int|string|array $chatId, int $messageThreadId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteForumTopic(int|string|array $chatId, int $messageThreadId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unpinAllForumTopicMessages(int|string|array $chatId, int $messageThreadId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editGeneralForumTopic(int|string|array $chatId, string $name = ''): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function closeGeneralForumTopic(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function reopenGeneralForumTopic(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function hideGeneralForumTopic(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unhideGeneralForumTopic(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function unpinAllGeneralForumTopicMessages(int|string|array $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function answerCallbackQuery(string $callbackQueryId, ?string $text = null, ?bool $showAlert = null, ?string $url = null, ?int $cacheTime = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function answerGuestQuery(string $guestQueryId, string $result): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getUserChatBoosts(int|string|array $chatId, int $userId = 0): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getBusinessConnection(string $businessConnectionId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getManagedBotToken(int $userId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function replaceManagedBotToken(int $userId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getManagedBotAccessSettings(int $userId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setManagedBotAccessSettings(int $userId, bool $isAccessRestricted, ?array $addedUserIds = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMyCommands(array $commands, ?array $scope = null, ?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteMyCommands(?array $scope = null, ?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getMyCommands(?array $scope = null, ?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMyName(?string $name = null, ?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getMyName(?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMyDescription(?string $description = null, ?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getMyDescription(?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMyShortDescription(?string $shortDescription = null, ?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getMyShortDescription(?string $languageCode = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMyProfilePhoto(string $photo): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function removeMyProfilePhoto(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatMenuButton(int|string|null $chatId = null, ?array $menuButton = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getChatMenuButton(int|string|null $chatId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMyDefaultAdministratorRights(?array $rights = null, ?bool $forChannels = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getMyDefaultAdministratorRights(?bool $forChannels = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getAvailableGifts(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function giftPremiumSubscription(int $userId, int $monthCount, int $starCount, ?string $text = null, ?string $textParseMode = null, ?array $textEntities = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function verifyUser(int $userId, ?string $customDescription = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
}
