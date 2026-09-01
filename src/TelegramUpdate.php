<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Support\Str;
use stdClass;

class TelegramUpdate
{
    protected stdClass $update;
    public ?array $matches = null;
    public array $commandArguments = [];
    public array $routeParameters = [];

    public function __construct($update) { $this->update = is_array($update) ? (object) $update : $update; }
    public function __get($key) { return $this->recursiveGet($this->update, $key); }
    protected function recursiveGet($object, string $key) { if (is_object($object) && property_exists($object, $key)) { $value = $object->{$key}; return (is_object($value) || is_array($value)) ? new static($value) : $value; } if (is_object($object)) { foreach ($object as $prop => $value) if (Str::snake($prop) === $key) return (is_object($value) || is_array($value)) ? new static($value) : $value; } return null; }
    public function __isset($key): bool { return $this->recursiveIsset($this->update, $key); }
    protected function recursiveIsset($object, string $key): bool { if (is_object($object) && property_exists($object, $key)) return true; if (is_object($object)) foreach ($object as $prop => $value) if (Str::snake($prop) === $key) return true; return false; }
    public function originalUpdate(): stdClass { return $this->update; }

    public function chatId()
    {
        return $this->message->chat->id
            ?? $this->callback_query->message->chat->id
            ?? $this->edited_message->chat->id
            ?? $this->channel_post->chat->id
            ?? $this->edited_channel_post->chat->id
            ?? $this->chat_member->chat->id
            ?? $this->my_chat_member->chat->id
            ?? $this->chat_join_request->chat->id
            ?? null;
    }

    public function userId()
    {
        return $this->message->from->id
            ?? $this->callback_query->from->id
            ?? $this->edited_message->from->id
            ?? $this->inline_query->from->id
            ?? $this->chat_member->from->id
            ?? $this->my_chat_member->from->id
            ?? $this->chat_join_request->from->id
            ?? null;
    }

    public function messageId()
    {
        return $this->message->message_id
            ?? $this->callback_query->message->message_id
            ?? $this->edited_message->message_id
            ?? $this->channel_post->message_id
            ?? $this->edited_channel_post->message_id
            ?? null;
    }

    public function text()
    {
        return $this->message->text
            ?? $this->edited_message->text
            ?? $this->channel_post->text
            ?? $this->edited_channel_post->text
            ?? $this->inline_query->query
            ?? $this->callback_query->data
            ?? null;
    }

    public function callbackQueryData(): ?string { return $this->callback_query->data ?? null; }
    public function commandArguments(): array { return $this->commandArguments; }
    public function routeParameters(): array { return $this->routeParameters; }
    public function routeParameter(string $name, mixed $default = null): mixed { return $this->routeParameters[$name] ?? $default; }
    public static function fromArray($data) { return new self($data); }
}
