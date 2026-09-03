<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Core;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramApiException;
use RuntimeException;

final class TelegramApiClient
{
    /**
     * Developer-facing positional parameter names for the first API methods.
     *
     * The registry remains the single source of truth for the complete API
     * method list and the existing metadata. These entries define the public
     * ergonomic order: required values first, optional values afterwards.
     *
     * @var array<string, list<string>>
     */
    private const DEVELOPER_PARAMETERS = [
        'getMe' => [],
        'logOut' => [],
        'close' => [],
        'sendMessage' => ['chat_id', 'text', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'parse_mode', 'entities', 'link_preview_options', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendMessageDraft' => ['chat_id', 'draft_id', 'text', 'message_thread_id', 'parse_mode', 'entities', 'link_preview_options', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'can_stop', 'keep_on_stop'],
        'sendPhoto' => ['chat_id', 'photo', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'caption', 'parse_mode', 'caption_entities', 'show_caption_above_media', 'has_spoiler', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendAudio' => ['chat_id', 'audio', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'duration', 'performer', 'title', 'caption', 'parse_mode', 'caption_entities', 'thumbnail', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendDocument' => ['chat_id', 'document', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'thumbnail', 'caption', 'parse_mode', 'caption_entities', 'disable_content_type_detection', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendVideo' => ['chat_id', 'video', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'duration', 'width', 'height', 'thumbnail', 'cover', 'start_timestamp', 'caption', 'parse_mode', 'caption_entities', 'show_caption_above_media', 'has_spoiler', 'supports_streaming', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendAnimation' => ['chat_id', 'animation', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'duration', 'width', 'height', 'thumbnail', 'caption', 'parse_mode', 'caption_entities', 'show_caption_above_media', 'has_spoiler', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendVoice' => ['chat_id', 'voice', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'caption', 'parse_mode', 'caption_entities', 'duration', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendVideoNote' => ['chat_id', 'video_note', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'duration', 'length', 'thumbnail', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendPaidMedia' => ['chat_id', 'star_count', 'media', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'payload', 'caption', 'parse_mode', 'caption_entities', 'show_caption_above_media', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendMediaGroup' => ['chat_id', 'media', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'disable_notification', 'protect_content'],
        'sendLivePhoto' => ['chat_id', 'live_photo', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'caption', 'parse_mode', 'caption_entities', 'show_caption_above_media', 'has_spoiler', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendContact' => ['chat_id', 'phone_number', 'first_name', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'last_name', 'vcard', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendLocation' => ['chat_id', 'latitude', 'longitude', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'horizontal_accuracy', 'live_period', 'heading', 'proximity_alert_radius', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendVenue' => ['chat_id', 'latitude', 'longitude', 'title', 'address', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'foursquare_id', 'foursquare_type', 'google_place_id', 'google_place_type', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendPoll' => ['chat_id', 'question', 'options', 'business_connection_id', 'message_thread_id', 'question_parse_mode', 'question_entities', 'is_anonymous', 'type', 'allows_multiple_answers', 'correct_option_id', 'explanation', 'explanation_parse_mode', 'explanation_entities', 'open_period', 'close_date', 'is_closed', 'media', 'explanation_media', 'members_only', 'country_codes', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup'],
        'sendDice' => ['chat_id', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'emoji', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup', 'ephemeral_message_parameters'],
        'sendChatAction' => ['chat_id', 'action', 'business_connection_id', 'message_thread_id'],
        'sendGift' => ['gift_id', 'user_id', 'chat_id', 'pay_for_upgrade', 'text', 'text_parse_mode', 'text_entities'],
        'sendGame' => ['chat_id', 'game_short_name', 'business_connection_id', 'message_thread_id', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'reply_parameters', 'reply_markup'],
        'sendInvoice' => ['chat_id', 'title', 'description', 'payload', 'currency', 'prices', 'message_thread_id', 'direct_messages_topic_id', 'provider_token', 'max_tip_amount', 'suggested_tip_amounts', 'start_parameter', 'provider_data', 'photo_url', 'photo_size', 'photo_width', 'photo_height', 'need_name', 'need_phone_number', 'need_email', 'need_shipping_address', 'send_phone_number_to_provider', 'send_email_to_provider', 'is_flexible', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup'],
        'sendRichMessage' => ['chat_id', 'rich_message', 'business_connection_id', 'message_thread_id', 'direct_messages_topic_id', 'ephemeral_message_parameters', 'disable_notification', 'protect_content', 'allow_paid_broadcast', 'message_effect_id', 'suggested_post_parameters', 'reply_parameters', 'reply_markup'],
        'sendRichMessageDraft' => ['chat_id', 'draft_id', 'rich_message', 'message_thread_id', 'ephemeral_message_parameters', 'can_stop', 'keep_on_stop'],
        'getUserProfilePhotos' => ['user_id', 'offset', 'limit'],
        'getUserProfileAudios' => ['user_id', 'offset', 'limit'],
        'setUserEmojiStatus' => ['user_id', 'emoji_status_custom_emoji_id', 'emoji_status_expiration_date'],
        'getFile' => ['file_id'],
        'banChatMember' => ['chat_id', 'user_id', 'until_date', 'revoke_messages'],
        'unbanChatMember' => ['chat_id', 'user_id', 'only_if_banned'],
        'restrictChatMember' => ['chat_id', 'user_id', 'permissions', 'use_independent_chat_permissions', 'until_date'],
        'promoteChatMember' => ['chat_id', 'user_id', 'is_anonymous', 'can_manage_chat', 'can_delete_messages', 'can_manage_video_chats', 'can_restrict_members', 'can_promote_members', 'can_change_info', 'can_invite_users', 'can_post_stories', 'can_edit_stories', 'can_post_messages', 'can_edit_messages', 'can_pin_messages', 'can_manage_topics', 'can_manage_direct_messages', 'can_manage_tags', 'can_send_welcome_messages'],
        'setChatAdministratorCustomTitle' => ['chat_id', 'user_id', 'custom_title'],
        'banChatSenderChat' => ['chat_id', 'sender_chat_id'],
        'unbanChatSenderChat' => ['chat_id', 'sender_chat_id'],
        'setChatPermissions' => ['chat_id', 'permissions', 'use_independent_chat_permissions'],
        'exportChatInviteLink' => ['chat_id'],
        'createChatInviteLink' => ['chat_id', 'name', 'expire_date', 'member_limit', 'creates_join_request'],
        'editChatInviteLink' => ['chat_id', 'invite_link', 'name', 'expire_date', 'member_limit', 'creates_join_request'],
        'revokeChatInviteLink' => ['chat_id', 'invite_link'],
        'approveChatJoinRequest' => ['chat_id', 'user_id'],
        'declineChatJoinRequest' => ['chat_id', 'user_id'],
        'answerChatJoinRequestQuery' => ['query_id', 'result'],
        'sendChatJoinRequestWebApp' => ['chat_join_request_query_id', 'web_app_url'],
        'setChatPhoto' => ['chat_id', 'photo'],
        'deleteChatPhoto' => ['chat_id'],
        'setChatTitle' => ['chat_id', 'title'],
        'setChatDescription' => ['chat_id', 'description'],
    ];

    public function __construct(
        private readonly ClientInterface $http,
        private readonly string $token,
        private readonly string $apiUrl = 'https://api.telegram.org',
    ) {
        if (trim($this->token) === '') {
            throw new RuntimeException('Telegram bot token is not configured.');
        }
    }

    public function call(string $method, array $parameters = []): mixed
    {
        if (!TelegramApiMethodRegistry::supports($method)) {
            throw new \BadMethodCallException(sprintf('Telegram API method [%s] is not supported.', $method));
        }

        $url = rtrim($this->apiUrl, '/') . '/bot' . $this->token . '/' . $method;
        $options = $this->buildRequestOptions($parameters);

        try {
            $response = $this->http->request('POST', $url, $options);
            $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $exception) {
            throw new TelegramApiException(
                sprintf('Telegram API request [%s] failed: %s', $method, $exception->getMessage()),
                (int) $exception->getCode(),
                [],
                $exception,
            );
        } catch (\JsonException $exception) {
            throw new TelegramApiException(
                sprintf('Telegram API returned invalid JSON for [%s].', $method),
                0,
                [],
                $exception,
            );
        }

        if (($payload['ok'] ?? false) !== true) {
            throw new TelegramApiException(
                (string) ($payload['description'] ?? sprintf('Telegram API method [%s] failed.', $method)),
                (int) ($payload['error_code'] ?? 0),
                is_array($payload['parameters'] ?? null) ? $payload['parameters'] : [],
            );
        }

        return $payload['result'] ?? null;
    }

    /**
     * Supports both the legacy associative-array API and the new ergonomic API:
     *
     *     $client->sendMessage($chatId, 'Hello');
     *     $client->sendMessage($chatId, 'Hello', parseMode: 'HTML');
     *     $client->sendMessage(['chat_id' => $chatId, 'text' => 'Hello']);
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (isset($arguments[0]) && is_array($arguments[0]) && $this->isAssociative($arguments[0])) {
            return $this->call($method, $arguments[0]);
        }

        return $this->call($method, $this->normalizeDeveloperArguments($method, $arguments));
    }

    /** @param list<mixed>|array<string, mixed> $arguments */
    private function normalizeDeveloperArguments(string $method, array $arguments): array
    {
        $names = self::DEVELOPER_PARAMETERS[$method] ?? $this->registryParameterNames($method);

        if ($names === []) {
            if ($arguments !== []) {
                throw new \InvalidArgumentException(sprintf('Method [%s] does not accept parameters.', $method));
            }
            return [];
        }

        $parameters = [];
        $position = 0;

        foreach ($arguments as $key => $value) {
            if (is_string($key)) {
                $parameters[$this->toApiParameterName($key)] = $value;
                continue;
            }

            if (!array_key_exists($position, $names)) {
                throw new \ArgumentCountError(sprintf('Too many arguments for Telegram API method [%s].', $method));
            }

            $parameters[$names[$position]] = $value;
            $position++;
        }

        $definition = TelegramApiMethodRegistry::parameters($method);
        foreach ($definition['required'] as $required) {
            if (!array_key_exists($required, $parameters)) {
                throw new \ArgumentCountError(sprintf('Missing required parameter [%s] for Telegram API method [%s].', $required, $method));
            }
        }

        return array_filter($parameters, static fn ($value): bool => $value !== null);
    }

    /** @return list<string> */
    private function registryParameterNames(string $method): array
    {
        $definition = TelegramApiMethodRegistry::parameters($method);
        return array_values(array_unique(array_merge($definition['required'], $definition['optional'])));
    }

    private function toApiParameterName(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }

    /** @param array<mixed> $value */
    private function isAssociative(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }

    /** @return array<string, mixed> */
    private function buildRequestOptions(array $parameters): array
    {
        if ($this->containsUpload($parameters)) {
            return ['multipart' => $this->toMultipart($parameters)];
        }

        return ['json' => $parameters];
    }

    private function containsUpload(array $parameters): bool
    {
        foreach ($parameters as $value) {
            if (is_resource($value) || $value instanceof \CURLFile) {
                return true;
            }
            if (is_array($value) && $this->containsUpload($value)) {
                return true;
            }
        }

        return false;
    }

    /** @return list<array{name: string, contents: mixed}> */
    private function toMultipart(array $parameters): array
    {
        $parts = [];
        foreach ($parameters as $name => $value) {
            $parts[] = ['name' => (string) $name, 'contents' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value];
        }

        return $parts;
    }
}
