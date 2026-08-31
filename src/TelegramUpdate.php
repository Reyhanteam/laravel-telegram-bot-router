<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Support\Str;
use stdClass;

class TelegramUpdate
{
    protected stdClass $update;

    public ?array $matches = null;

    /**
     * Arguments provided after the Telegram command.
     */
    public array $commandArguments = [];

    public function __construct($update)
    {
        $this->update = is_array($update) ? (object) $update : $update;
    }

    public function __get($key)
    {
        return $this->recursiveGet($this->update, $key);
    }

    protected function recursiveGet($object, string $key)
    {
        if (is_object($object) && property_exists($object, $key)) {
            $value = $object->{$key};

            if (is_object($value) || is_array($value)) {
                return new static($value);
            }

            return $value;
        }

        if (is_object($object)) {
            foreach ($object as $prop => $value) {
                if (Str::snake($prop) === $key) {
                    if (is_object($value) || is_array($value)) {
                        return new static($value);
                    }

                    return $value;
                }
            }
        }

        return null;
    }

    public function __isset($key): bool
    {
        return $this->recursiveIsset($this->update, $key);
    }

    protected function recursiveIsset($object, string $key): bool
    {
        if (is_object($object) && property_exists($object, $key)) {
            return true;
        } elseif (is_object($object)) {
            foreach ($object as $prop => $value) {
                if (Str::snake($prop) === $key) {
                    return true;
                }
            }
        }

        return false;
    }

    public function originalUpdate(): stdClass
    {
        return $this->update;
    }

    public function chatId()
    {
        return $this->message->chat->id
            ?? $this->callback_query->message->chat->id
            ?? $this->edited_message->chat->id
            ?? $this->channel_post->chat->id
            ?? null;
    }

    public function userId()
    {
        return $this->message->from->id
            ?? $this->callback_query->from->id
            ?? null;
    }

    public function messageId()
    {
        return $this->message->message_id
            ?? $this->callback_query->message->message_id
            ?? $this->edited_message->message_id
            ?? null;
    }

    public function text()
    {
        return $this->message->text
            ?? $this->callback_query->data
            ?? null;
    }

    public function callbackQueryData(): ?string
    {
        return $this->callback_query->data ?? null;
    }

    /**
     * Get the arguments supplied after the Telegram command.
     *
     * Example: /start one two
     * returns ['one', 'two'].
     */
    public function commandArguments(): array
    {
        return $this->commandArguments;
    }

    public static function fromArray($data)
    {
        return new self($data);
    }
}
