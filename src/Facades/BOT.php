<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

/**
 * Developer-facing Telegram Bot API.
 *
 * Positional arguments are mapped to Telegram parameter names internally.
 * Legacy associative arrays remain supported for backwards compatibility.
 *
 * @see https://core.telegram.org/bots/api
 * @method mixed getMe()
 * @method mixed logOut()
 * @method mixed close()
 * @method mixed sendMessage(mixed $chatId, mixed $text, mixed ...$optional)
 * @method mixed sendMessageDraft(mixed $chatId, mixed $draftId, mixed $text, mixed ...$optional)
 * @method mixed sendPhoto(mixed $chatId, mixed $photo, mixed ...$optional)
 * @method mixed sendAudio(mixed $chatId, mixed $audio, mixed ...$optional)
 * @method mixed sendDocument(mixed $chatId, mixed $document, mixed ...$optional)
 * @method mixed sendVideo(mixed $chatId, mixed $video, mixed ...$optional)
 * @method mixed sendAnimation(mixed $chatId, mixed $animation, mixed ...$optional)
 * @method mixed sendVoice(mixed $chatId, mixed $voice, mixed ...$optional)
 * @method mixed sendVideoNote(mixed $chatId, mixed $videoNote, mixed ...$optional)
 * @method mixed sendPaidMedia(mixed $chatId, mixed $starCount, mixed $media, mixed ...$optional)
 * @method mixed sendMediaGroup(mixed $chatId, mixed $media, mixed ...$optional)
 * @method mixed sendLivePhoto(mixed $chatId, mixed $livePhoto, mixed ...$optional)
 * @method mixed sendContact(mixed $chatId, mixed $phoneNumber, mixed $firstName, mixed ...$optional)
 * @method mixed sendLocation(mixed $chatId, mixed $latitude, mixed $longitude, mixed ...$optional)
 * @method mixed sendVenue(mixed $chatId, mixed $latitude, mixed $longitude, mixed $title, mixed $address, mixed ...$optional)
 * @method mixed sendPoll(mixed $chatId, mixed $question, mixed $options, mixed ...$optional)
 * @method mixed sendDice(mixed $chatId, mixed ...$optional)
 * @method mixed sendChatAction(mixed $chatId, mixed $action, mixed ...$optional)
 * @method mixed sendGift(mixed $giftId, mixed ...$optional)
 * @method mixed sendGame(mixed $chatId, mixed $gameShortName, mixed ...$optional)
 * @method mixed sendInvoice(mixed $chatId, mixed $title, mixed $description, mixed $payload, mixed $currency, mixed $prices, mixed ...$optional)
 * @method mixed sendRichMessage(mixed $chatId, mixed $richMessage, mixed ...$optional)
 * @method mixed sendRichMessageDraft(mixed $chatId, mixed $draftId, mixed $richMessage, mixed ...$optional)
 * @method mixed getUserProfilePhotos(mixed $userId, mixed ...$optional)
 * @method mixed getUserProfileAudios(mixed $userId, mixed ...$optional)
 * @method mixed setUserEmojiStatus(mixed $userId, mixed ...$optional)
 * @method mixed getFile(mixed $fileId, mixed ...$optional)
 * @method mixed banChatMember(mixed $chatId, mixed $userId, mixed ...$optional)
 * @method mixed unbanChatMember(mixed $chatId, mixed $userId, mixed ...$optional)
 * @method mixed restrictChatMember(mixed $chatId, mixed $userId, mixed $permissions, mixed ...$optional)
 * @method mixed promoteChatMember(mixed $chatId, mixed $userId, mixed ...$optional)
 * @method mixed setChatAdministratorCustomTitle(mixed $chatId, mixed $userId, mixed $customTitle, mixed ...$optional)
 * @method mixed banChatSenderChat(mixed $chatId, mixed $senderChatId, mixed ...$optional)
 * @method mixed unbanChatSenderChat(mixed $chatId, mixed $senderChatId, mixed ...$optional)
 * @method mixed setChatPermissions(mixed $chatId, mixed $permissions, mixed ...$optional)
 * @method mixed exportChatInviteLink(mixed $chatId, mixed ...$optional)
 * @method mixed createChatInviteLink(mixed $chatId, mixed ...$optional)
 * @method mixed editChatInviteLink(mixed $chatId, mixed $inviteLink, mixed ...$optional)
 * @method mixed revokeChatInviteLink(mixed $chatId, mixed $inviteLink, mixed ...$optional)
 * @method mixed approveChatJoinRequest(mixed $chatId, mixed $userId, mixed ...$optional)
 * @method mixed declineChatJoinRequest(mixed $chatId, mixed $userId, mixed ...$optional)
 * @method mixed answerChatJoinRequestQuery(mixed $queryId, mixed $result, mixed ...$optional)
 * @method mixed sendChatJoinRequestWebApp(mixed $chatJoinRequestQueryId, mixed $webAppUrl, mixed ...$optional)
 * @method mixed setChatPhoto(mixed $chatId, mixed $photo, mixed ...$optional)
 * @method mixed deleteChatPhoto(mixed $chatId, mixed ...$optional)
 * @method mixed setChatTitle(mixed $chatId, mixed $title, mixed ...$optional)
 * @method mixed setChatDescription(mixed $chatId, mixed $description, mixed ...$optional)
 * @method mixed pinChatMessage(mixed $chatId, mixed $messageId, mixed $businessConnectionId = null, mixed $disableNotification = null)
 * @method mixed unpinChatMessage(mixed $chatId, mixed $businessConnectionId = null, mixed $messageId = null)
 * @method mixed unpinAllChatMessages(mixed $chatId, mixed ...$optional)
 * @method mixed leaveChat(mixed $chatId, mixed ...$optional)
 * @method mixed getChat(mixed $chatId, mixed ...$optional)
 * @method mixed getChatAdministrators(mixed $chatId, mixed $returnBots = null)
 * @method mixed getChatMemberCount(mixed $chatId, mixed ...$optional)
 * @method mixed getChatMember(mixed $chatId, mixed $userId, mixed ...$optional)
 * @method mixed getUserPersonalChatMessages(mixed $userId, mixed $limit, mixed ...$optional)
 * @method mixed setChatStickerSet(mixed $chatId, mixed $stickerSetName, mixed ...$optional)
 * @method mixed deleteChatStickerSet(mixed $chatId, mixed ...$optional)
 * @method mixed getForumTopicIconStickers()
 * @method mixed createForumTopic(mixed $chatId, mixed $name, mixed ...$optional)
 * @method mixed editForumTopic(mixed $chatId, mixed $messageThreadId, mixed ...$optional)
 * @method mixed closeForumTopic(mixed $chatId, mixed $messageThreadId, mixed ...$optional)
 * @method mixed reopenForumTopic(mixed $chatId, mixed $messageThreadId, mixed ...$optional)
 * @method mixed deleteForumTopic(mixed $chatId, mixed $messageThreadId, mixed ...$optional)
 * @method mixed unpinAllForumTopicMessages(mixed $chatId, mixed $messageThreadId, mixed ...$optional)
 * @method mixed editGeneralForumTopic(mixed $chatId, mixed $name, mixed ...$optional)
 * @method mixed closeGeneralForumTopic(mixed $chatId, mixed ...$optional)
 * @method mixed reopenGeneralForumTopic(mixed $chatId, mixed ...$optional)
 * @method mixed hideGeneralForumTopic(mixed $chatId, mixed ...$optional)
 * @method mixed unhideGeneralForumTopic(mixed $chatId, mixed ...$optional)
 * @method mixed unpinAllGeneralForumTopicMessages(mixed $chatId, mixed ...$optional)
 * @method mixed answerCallbackQuery(mixed $callbackQueryId, mixed ...$optional)
 * @method mixed answerGuestQuery(mixed $guestQueryId, mixed $result, mixed ...$optional)
 * @method mixed getUserChatBoosts(mixed $chatId, mixed $userId)
 * @method mixed getBusinessConnection(mixed $businessConnectionId, mixed ...$optional)
 * @method mixed getManagedBotToken(mixed $userId, mixed ...$optional)
 * @method mixed replaceManagedBotToken(mixed $userId, mixed ...$optional)
 * @method mixed getManagedBotAccessSettings(mixed $userId, mixed ...$optional)
 * @method mixed setManagedBotAccessSettings(mixed $userId, mixed $isAccessRestricted, mixed $addedUserIds = null)
 * @method mixed setMyCommands(mixed $commands, mixed $scope = null, mixed $languageCode = null)
 * @method mixed deleteMyCommands(mixed $scope = null, mixed $languageCode = null)
 * @method mixed getMyCommands(mixed $scope = null, mixed $languageCode = null)
 * @method mixed setMyName(mixed $name = null, mixed $languageCode = null)
 * @method mixed getMyName(mixed $languageCode = null)
 * @method mixed setMyDescription(mixed $description = null, mixed $languageCode = null)
 * @method mixed getMyDescription(mixed $languageCode = null)
 * @method mixed setMyShortDescription(mixed $shortDescription = null, mixed $languageCode = null)
 * @method mixed getMyShortDescription(mixed $languageCode = null)
 * @method mixed setMyProfilePhoto(mixed $photo, mixed ...$optional)
 * @method mixed removeMyProfilePhoto()
 * @method mixed setChatMenuButton(mixed $chatId = null, mixed $menuButton = null)
 * @method mixed getChatMenuButton(mixed $chatId = null)
 * @method mixed setMyDefaultAdministratorRights(mixed $rights = null, mixed $forChannels = null)
 * @method mixed getMyDefaultAdministratorRights(mixed $forChannels = null)
 * @method mixed getAvailableGifts()
 * @method mixed giftPremiumSubscription(mixed $userId, mixed $monthCount, mixed $starCount, mixed $text = null, mixed $textParseMode = null, mixed $textEntities = null)
 * @method mixed verifyUser(mixed $userId, mixed $customDescription = null)
 */
final class BOT extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }
}
