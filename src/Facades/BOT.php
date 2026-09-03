<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

/**
 * Developer-friendly Telegram Bot API facade.
 *
 * Runtime dispatch is handled by Laravel's Facade magic. The @method
 * declarations below are IDE metadata. They provide autocomplete, real
 * parameter names, required/optional information, scalar types and named
 * argument support without duplicating HTTP request logic.
 *
 * Legacy associative-array calls remain supported by TelegramApiClient.
 *
 * @see https://core.telegram.org/bots/api
 * @method static mixed getMe()
 * @method static mixed logOut()
 * @method static mixed close()
 * @method static mixed sendMessage(int|string $chatId, string $text, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $parseMode = null, ?array $entities = null, ?array $linkPreviewOptions = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendMessageDraft(int|string $chatId, int $draftId, ?string $text = null, ?int $messageThreadId = null, ?string $parseMode = null, ?array $entities = null, ?array $linkPreviewOptions = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?bool $canStop = null, ?bool $keepOnStop = null)
 * @method static mixed sendPhoto(int|string $chatId, string $photo, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendAudio(int|string $chatId, string $audio, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $duration = null, ?string $performer = null, ?string $title = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?string $thumbnail = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendDocument(int|string $chatId, string $document, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $thumbnail = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $disableContentTypeDetection = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendVideo(int|string $chatId, string $video, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $duration = null, ?int $width = null, ?int $height = null, ?string $thumbnail = null, ?string $cover = null, ?int $startTimestamp = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $supportsStreaming = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendAnimation(int|string $chatId, string $animation, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?int $duration = null, ?int $width = null, ?int $height = null, ?string $thumbnail = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendVoice(int|string $chatId, string $voice, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?int $duration = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendVideoNote(int|string $chatId, string $videoNote, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $duration = null, ?int $length = null, ?string $thumbnail = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendPaidMedia(int|string $chatId, int $starCount, array $media, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $payload = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendMediaGroup(int|string $chatId, array $media, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?bool $disableNotification = null, ?bool $protectContent = null)
 * @method static mixed sendLivePhoto(int|string $chatId, string $livePhoto, string $photo, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $caption = null, ?string $parseMode = null, ?array $captionEntities = null, ?bool $showCaptionAboveMedia = null, ?bool $hasSpoiler = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendContact(int|string $chatId, string $phoneNumber, string $firstName, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $lastName = null, ?string $vcard = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendLocation(int|string $chatId, float $latitude, float $longitude, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?float $horizontalAccuracy = null, ?int $livePeriod = null, ?int $heading = null, ?int $proximityAlertRadius = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendVenue(int|string $chatId, float $latitude, float $longitude, string $title, string $address, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $foursquareId = null, ?string $foursquareType = null, ?string $googlePlaceId = null, ?string $googlePlaceType = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendPoll(int|string $chatId, string $question, array $options, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?string $questionParseMode = null, ?array $questionEntities = null, ?bool $isAnonymous = null, ?string $type = null, ?bool $allowsMultipleAnswers = null, ?int $correctOptionId = null, ?string $explanation = null, ?string $explanationParseMode = null, ?array $explanationEntities = null, ?int $openPeriod = null, ?int $closeDate = null, ?bool $isClosed = null, ?array $media = null, ?array $explanationMedia = null, ?bool $membersOnly = null, ?array $countryCodes = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null)
 * @method static mixed sendDice(int|string $chatId, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $emoji = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null, ?array $ephemeralMessageParameters = null)
 * @method static mixed sendChatAction(int|string $chatId, string $action, ?string $businessConnectionId = null, ?int $messageThreadId = null)
 * @method static mixed sendGift(string $giftId, ?int $userId = null, int|string|null $chatId = null, ?bool $payForUpgrade = null, ?string $text = null, ?string $textParseMode = null, ?array $textEntities = null)
 * @method static mixed sendGame(int|string $chatId, string $gameShortName, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $replyParameters = null, ?array $replyMarkup = null)
 * @method static mixed sendInvoice(int|string $chatId, string $title, string $description, string $payload, string $currency, array $prices, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?string $providerToken = null, ?int $maxTipAmount = null, ?array $suggestedTipAmounts = null, ?string $startParameter = null, ?string $providerData = null, ?string $photoUrl = null, ?int $photoSize = null, ?int $photoWidth = null, ?int $photoHeight = null, ?bool $needName = null, ?bool $needPhoneNumber = null, ?bool $needEmail = null, ?bool $needShippingAddress = null, ?bool $sendPhoneNumberToProvider = null, ?bool $sendEmailToProvider = null, ?bool $isFlexible = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null)
 * @method static mixed sendRichMessage(int|string $chatId, array $richMessage, ?string $businessConnectionId = null, ?int $messageThreadId = null, ?int $directMessagesTopicId = null, ?array $ephemeralMessageParameters = null, ?bool $disableNotification = null, ?bool $protectContent = null, ?bool $allowPaidBroadcast = null, ?string $messageEffectId = null, ?array $suggestedPostParameters = null, ?array $replyParameters = null, ?array $replyMarkup = null)
 * @method static mixed sendRichMessageDraft(int|string $chatId, int $draftId, array $richMessage, ?int $messageThreadId = null, ?array $ephemeralMessageParameters = null, ?bool $canStop = null, ?bool $keepOnStop = null)
 * @method static mixed getUserProfilePhotos(int $userId, ?int $offset = null, ?int $limit = null)
 * @method static mixed getUserProfileAudios(int $userId, ?int $offset = null, ?int $limit = null)
 * @method static mixed setUserEmojiStatus(int $userId, string $emojiStatusCustomEmojiId, ?int $emojiStatusExpirationDate = null)
 * @method static mixed getFile(string $fileId)
 * @method static mixed banChatMember(int|string $chatId, int $userId, ?int $untilDate = null, ?bool $revokeMessages = null)
 * @method static mixed unbanChatMember(int|string $chatId, int $userId, ?bool $onlyIfBanned = null)
 * @method static mixed restrictChatMember(int|string $chatId, int $userId, array $permissions, ?bool $useIndependentChatPermissions = null, ?int $untilDate = null)
 * @method static mixed promoteChatMember(int|string $chatId, int $userId, ?bool $isAnonymous = null, ?bool $canManageChat = null, ?bool $canDeleteMessages = null, ?bool $canManageVideoChats = null, ?bool $canRestrictMembers = null, ?bool $canPromoteMembers = null, ?bool $canChangeInfo = null, ?bool $canInviteUsers = null, ?bool $canPostStories = null, ?bool $canEditStories = null, ?bool $canPostMessages = null, ?bool $canEditMessages = null, ?bool $canPinMessages = null, ?bool $canManageTopics = null, ?bool $canManageDirectMessages = null, ?bool $canManageTags = null, ?bool $canSendWelcomeMessages = null)
 * @method static mixed setChatAdministratorCustomTitle(int|string $chatId, int $userId, string $customTitle)
 * @method static mixed banChatSenderChat(int|string $chatId, int|string $senderChatId)
 * @method static mixed unbanChatSenderChat(int|string $chatId, int|string $senderChatId)
 * @method static mixed setChatPermissions(int|string $chatId, array $permissions, ?bool $useIndependentChatPermissions = null)
 * @method static mixed exportChatInviteLink(int|string $chatId)
 * @method static mixed createChatInviteLink(int|string $chatId, ?string $name = null, ?int $expireDate = null, ?int $memberLimit = null, ?bool $createsJoinRequest = null)
 * @method static mixed editChatInviteLink(int|string $chatId, string $inviteLink, ?string $name = null, ?int $expireDate = null, ?int $memberLimit = null, ?bool $createsJoinRequest = null)
 * @method static mixed revokeChatInviteLink(int|string $chatId, string $inviteLink)
 * @method static mixed approveChatJoinRequest(int|string $chatId, int $userId)
 * @method static mixed declineChatJoinRequest(int|string $chatId, int $userId)
 * @method static mixed answerChatJoinRequestQuery(string $queryId, string $result)
 * @method static mixed sendChatJoinRequestWebApp(string $chatJoinRequestQueryId, string $webAppUrl)
 * @method static mixed setChatPhoto(int|string $chatId, string $photo)
 * @method static mixed deleteChatPhoto(int|string $chatId)
 * @method static mixed setChatTitle(int|string $chatId, string $title)
 * @method static mixed setChatDescription(int|string $chatId, ?string $description = null)
 * @method static mixed pinChatMessage(int|string $chatId, int $messageId, ?string $businessConnectionId = null, ?bool $disableNotification = null)
 * @method static mixed unpinChatMessage(int|string $chatId, ?string $businessConnectionId = null, ?int $messageId = null)
 * @method static mixed unpinAllChatMessages(int|string $chatId)
 * @method static mixed leaveChat(int|string $chatId)
 * @method static mixed getChat(int|string $chatId)
 * @method static mixed getChatAdministrators(int|string $chatId, ?bool $returnBots = null)
 * @method static mixed getChatMemberCount(int|string $chatId)
 * @method static mixed getChatMember(int|string $chatId, int $userId)
 * @method static mixed getUserPersonalChatMessages(int $userId, int $limit)
 * @method static mixed setChatStickerSet(int|string $chatId, string $stickerSetName)
 * @method static mixed deleteChatStickerSet(int|string $chatId)
 * @method static mixed getForumTopicIconStickers()
 * @method static mixed createForumTopic(int|string $chatId, string $name, ?int $iconColor = null, ?string $iconCustomEmojiId = null)
 * @method static mixed editForumTopic(int|string $chatId, int $messageThreadId, ?string $name = null, ?string $iconCustomEmojiId = null)
 * @method static mixed closeForumTopic(int|string $chatId, int $messageThreadId)
 * @method static mixed reopenForumTopic(int|string $chatId, int $messageThreadId)
 * @method static mixed deleteForumTopic(int|string $chatId, int $messageThreadId)
 * @method static mixed unpinAllForumTopicMessages(int|string $chatId, int $messageThreadId)
 * @method static mixed editGeneralForumTopic(int|string $chatId, string $name)
 * @method static mixed closeGeneralForumTopic(int|string $chatId)
 * @method static mixed reopenGeneralForumTopic(int|string $chatId)
 * @method static mixed hideGeneralForumTopic(int|string $chatId)
 * @method static mixed unhideGeneralForumTopic(int|string $chatId)
 * @method static mixed unpinAllGeneralForumTopicMessages(int|string $chatId)
 * @method static mixed answerCallbackQuery(string $callbackQueryId, ?string $text = null, ?bool $showAlert = null, ?string $url = null, ?int $cacheTime = null)
 * @method static mixed answerGuestQuery(string $guestQueryId, string $result)
 * @method static mixed getUserChatBoosts(int|string $chatId, int $userId)
 * @method static mixed getBusinessConnection(string $businessConnectionId)
 * @method static mixed getManagedBotToken(int $userId)
 * @method static mixed replaceManagedBotToken(int $userId)
 * @method static mixed getManagedBotAccessSettings(int $userId)
 * @method static mixed setManagedBotAccessSettings(int $userId, bool $isAccessRestricted, ?array $addedUserIds = null)
 * @method static mixed setMyCommands(array $commands, ?array $scope = null, ?string $languageCode = null)
 * @method static mixed deleteMyCommands(?array $scope = null, ?string $languageCode = null)
 * @method static mixed getMyCommands(?array $scope = null, ?string $languageCode = null)
 * @method static mixed setMyName(?string $name = null, ?string $languageCode = null)
 * @method static mixed getMyName(?string $languageCode = null)
 * @method static mixed setMyDescription(?string $description = null, ?string $languageCode = null)
 * @method static mixed getMyDescription(?string $languageCode = null)
 * @method static mixed setMyShortDescription(?string $shortDescription = null, ?string $languageCode = null)
 * @method static mixed getMyShortDescription(?string $languageCode = null)
 * @method static mixed setMyProfilePhoto(string $photo)
 * @method static mixed removeMyProfilePhoto()
 * @method static mixed setChatMenuButton(int|string|null $chatId = null, ?array $menuButton = null)
 * @method static mixed getChatMenuButton(int|string|null $chatId = null)
 * @method static mixed setMyDefaultAdministratorRights(?array $rights = null, ?bool $forChannels = null)
 * @method static mixed getMyDefaultAdministratorRights(?bool $forChannels = null)
 * @method static mixed getAvailableGifts()
 * @method static mixed giftPremiumSubscription(int $userId, int $monthCount, int $starCount, ?string $text = null, ?string $textParseMode = null, ?array $textEntities = null)
 * @method static mixed verifyUser(int $userId, ?string $customDescription = null)
 */
final class BOT extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }
}
