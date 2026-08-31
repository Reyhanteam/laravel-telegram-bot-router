<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation;

use InvalidArgumentException;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ConversationInput
{
    public function __construct(
        protected TelegramUpdate $update,
        protected array $data = [],
    ) {
    }

    public function text(?string $default = null): ?string
    {
        return $this->value($default);
    }

    public function value(?string $default = null): ?string
    {
        $value = $this->update->text();
        return $value === null ? $default : (string) $value;
    }

    public function required(): string
    {
        $value = trim((string) $this->value(''));
        if ($value === '') {
            throw new InvalidArgumentException('Conversation input is required.');
        }

        return $value;
    }

    public function string(): string
    {
        return (string) $this->value('');
    }

    public function integer(): int
    {
        $value = trim($this->required());

        if (!preg_match('/^-?\d+$/', $value)) {
            throw new InvalidArgumentException('Conversation input must be an integer.');
        }

        return (int) $value;
    }

    public function matches(string $pattern): string
    {
        $value = $this->required();

        if (preg_match($pattern, $value) !== 1) {
            throw new InvalidArgumentException('Conversation input does not match the required pattern.');
        }

        return $value;
    }

    public function minLength(int $length): string
    {
        $value = $this->required();

        if (mb_strlen($value) < $length) {
            throw new InvalidArgumentException("Conversation input must contain at least {$length} characters.");
        }

        return $value;
    }

    public function maxLength(int $length): string
    {
        $value = $this->required();

        if (mb_strlen($value) > $length) {
            throw new InvalidArgumentException("Conversation input must contain no more than {$length} characters.");
        }

        return $value;
    }

    public function in(array $allowed): string
    {
        $value = $this->required();

        if (!in_array($value, $allowed, true)) {
            throw new InvalidArgumentException('Conversation input is not an allowed value.');
        }

        return $value;
    }

    public function data(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->data;
        }

        return $this->data[$key] ?? $default;
    }

    public function update(): TelegramUpdate
    {
        return $this->update;
    }
}
