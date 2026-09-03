<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

/**
 * Remaining Telegram Bot API methods exposed as real static facade methods.
 *
 * @see https://core.telegram.org/bots/api
 */
trait TelegramApiAdditionalMethods
{
    public static function getUpdates(?int $offset = null, ?int $limit = null, ?int $timeout = null, ?array $allowedUpdates = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setWebhook(string $url, mixed $certificate = null, ?string $ipAddress = null, ?int $maxConnections = null, ?array $allowedUpdates = null, ?bool $dropPendingUpdates = null, ?string $secretToken = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteWebhook(?bool $dropPendingUpdates = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getWebhookInfo(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function forwardMessage(int|string $chatId, int|string $fromChatId, int $messageId, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $videoStartTimestamp = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function forwardMessages(int|string $chatId, int|string $fromChatId, array $messageIds, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?bool $disableNotification = null, ?bool $protectContent = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function copyMessage(int|string $chatId, int|string $fromChatId, int $messageId, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $videoStartTimestamp = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function copyMessages(int|string $chatId, int|string $fromChatId, array $messageIds, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?bool $disableNotification = null, ?bool $protectContent = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendSticker(int|string $chatId, mixed $sticker, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $receiverUserId = null, ?string $callbackQueryId = null, ?string $emoji = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function sendChecklist(string $businessConnectionId, int|string $chatId, array $checklist, ?bool $disableNotification = null, ?bool $protectContent = null, ?string $messageEffectId = null, ?array $replyParameters = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setMessageReaction(int|string $chatId, int $messageId, ?array $reaction = null, ?bool $isBig = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setChatMemberTag(int|string $chatId, int $userId, ?string $tag = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }

    // Developer-friendly order: chatId, messageId, content, then business/optional parameters.
    public static function editMessageText(int|string|null $chatId = null, ?int $messageId = null, ?string $text = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?string $parseMode = null, ?array $entities = null, ?array $linkPreviewOptions = null, ?array $richMessage = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editMessageCaption(int|string|null $chatId = null, ?int $messageId = null, ?string $caption = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editMessageMedia(array $media, int|string|null $chatId = null, ?int $messageId = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editMessageLivePhoto(array $media, int|string|null $chatId = null, ?int $messageId = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editMessageLiveLocation(float $latitude, float $longitude, int|string|null $chatId = null, ?int $messageId = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?int $livePeriod = null, ?float $horizontalAccuracy = null, ?int $heading = null, ?int $proximityAlertRadius = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function stopMessageLiveLocation(int|string|null $chatId = null, ?int $messageId = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editMessageReplyMarkup(int|string|null $chatId = null, ?int $messageId = null, ?string $inlineMessageId = null, ?string $businessConnectionId = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editMessageChecklist(string $businessConnectionId, int|string $chatId, int $messageId, array $checklist, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function stopPoll(int|string $chatId, int $messageId, ?string $businessConnectionId = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteMessage(int|string $chatId, int $messageId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteMessages(int|string $chatId, array $messageIds): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteEphemeralMessage(int|string $chatId, int $receiverUserId, int $ephemeralMessageId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteMessageReaction(int|string $chatId, int $messageId, ?int $userId = null, ?int $actorChatId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteAllMessageReactions(int|string $chatId, ?int $userId = null, ?int $actorChatId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }

    public static function editEphemeralMessageText(int|string $chatId, int $receiverUserId, int $ephemeralMessageId, ?string $text = null, ?string $parseMode = null, ?array $entities = null, ?array $richMessage = null, ?array $linkPreviewOptions = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editEphemeralMessageMedia(int|string $chatId, int $receiverUserId, int $ephemeralMessageId, array $media, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editEphemeralMessageCaption(int|string $chatId, int $receiverUserId, int $ephemeralMessageId, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editEphemeralMessageReplyMarkup(int|string $chatId, int $receiverUserId, int $ephemeralMessageId, ?array $replyMarkup = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }

    public static function getStickerSet(string $name): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getCustomEmojiStickers(array $customEmojiIds): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function uploadStickerFile(int $userId, mixed $sticker, string $stickerFormat): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function createNewStickerSet(int $userId, string $name, string $title, array $stickers, ?string $stickerType = null, ?bool $needsRepainting = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function addStickerToSet(int $userId, string $name, array $sticker): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setStickerPositionInSet(string $sticker, int $position): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteStickerFromSet(string $sticker): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function replaceStickerInSet(int $userId, string $name, string $oldSticker, array $sticker): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setStickerEmojiList(string $sticker, array $emojiList): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setStickerKeywords(string $sticker, ?array $keywords = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setStickerMaskPosition(string $sticker, ?array $maskPosition = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setStickerSetTitle(string $name, string $title): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setStickerSetThumbnail(string $name, int $userId, string $format, mixed $thumbnail = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setCustomEmojiStickerSetThumbnail(string $name, ?string $customEmojiId = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteStickerSet(string $name): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function answerInlineQuery(string $inlineQueryId, array $results, ?int $cacheTime = null, ?bool $isPersonal = null, ?string $nextOffset = null, ?array $button = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function answerWebAppQuery(string $webAppQueryId, array $result): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function savePreparedInlineMessage(int $userId, array $result, ?bool $allowUserChats = null, ?bool $allowBotChats = null, ?bool $allowGroupChats = null, ?bool $allowChannelChats = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function savePreparedKeyboardButton(int $userId, array $button): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function postStory(string $businessConnectionId, array $content, int $activePeriod, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?array $areas = null, ?bool $postToChatPage = null, ?bool $protectContent = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function repostStory(string $businessConnectionId, int $fromChatId, int $fromStoryId, int $activePeriod, ?bool $postToChatPage = null, ?bool $protectContent = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editStory(string $businessConnectionId, int $storyId, array $content, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?array $areas = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteStory(string $businessConnectionId, int $storyId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getChatGifts(int|string $chatId, ?bool $excludeUnsaved = null, ?bool $excludeSaved = null, ?bool $excludeUnlimited = null, ?bool $excludeLimitedUpgradable = null, ?bool $excludeLimitedNonUpgradable = null, ?bool $excludeFromBlockchain = null, ?bool $excludeUnique = null, ?bool $sortByPrice = null, ?string $offset = null, ?int $limit = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getBusinessAccountStarBalance(string $businessConnectionId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function transferBusinessAccountStars(string $businessConnectionId, int $starCount): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getBusinessAccountGifts(string $businessConnectionId, ?bool $excludeUnsaved = null, ?bool $excludeSaved = null, ?bool $excludeUnlimited = null, ?bool $excludeLimitedUpgradable = null, ?bool $excludeLimitedNonUpgradable = null, ?bool $excludeFromBlockchain = null, ?bool $excludeUnique = null, ?bool $sortByPrice = null, ?string $offset = null, ?int $limit = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setBusinessAccountName(string $businessConnectionId, string $firstName, ?string $lastName = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setBusinessAccountUsername(string $businessConnectionId, ?string $username = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setBusinessAccountBio(string $businessConnectionId, ?string $bio = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setBusinessAccountProfilePhoto(string $businessConnectionId, array $photo, ?bool $isPublic = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function removeBusinessAccountProfilePhoto(string $businessConnectionId, ?bool $isPublic = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function setBusinessAccountGiftSettings(string $businessConnectionId, bool $showGiftButton, array $acceptedGiftTypes): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function readBusinessMessage(string $businessConnectionId, int $chatId, int $messageId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function deleteBusinessMessages(string $businessConnectionId, array $messageIds): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getMyStarBalance(): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getStarTransactions(?int $offset = null, ?int $limit = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function refundStarPayment(int $userId, string $telegramPaymentChargeId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function editUserStarSubscription(int $userId, string $telegramPaymentChargeId, bool $isCanceled): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function getUserGifts(int $userId, ?bool $excludeUnlimited = null, ?bool $excludeLimitedUpgradable = null, ?bool $excludeLimitedNonUpgradable = null, ?bool $excludeFromBlockchain = null, ?bool $excludeUnique = null, ?bool $sortByPrice = null, ?string $offset = null, ?int $limit = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function transferGift(string $businessConnectionId, string $ownedGiftId, int $newOwnerChatId, ?int $starCount = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function convertGiftToStars(string $businessConnectionId, string $ownedGiftId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function upgradeGift(string $businessConnectionId, string $ownedGiftId, ?bool $keepOriginalDetails = null, ?int $starCount = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function verifyChat(int|string $chatId, ?string $customDescription = null): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function removeUserVerification(int $userId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
    public static function removeChatVerification(int|string $chatId): mixed { return static::dispatch(__FUNCTION__, func_get_args()); }
}
