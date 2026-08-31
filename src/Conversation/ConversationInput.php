<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation;

use InvalidArgumentException;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ConversationInput
{
    protected ?string $validatedValue = null;

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
        if ($this->validatedValue !== null) {
            return $this->validatedValue;
        }

        $value = $this->update->text();

        return $value === null ? $default : (string) $value;
    }

    public function required(): static
    {
        $value = trim((string) $this->value(''));

        if ($value === '') {
            throw new InvalidArgumentException('Conversation input is required.');
        }

        $this->validatedValue = $value;

        return $this;
    }

    public function string(): static
    {
        $this->validatedValue = (string) $this->value('');

        return $this;
    }

    public function integer(): static
    {
        $value = trim((string) $this->value(''));

        if (!preg_match('/^-?\d+$/', $value)) {
            throw new InvalidArgumentException('Conversation input must be an integer.');
        }

        $this->validatedValue = $value;

        return $this;
    }

    public function matches(string $pattern): static
    {
        $value = $this->required()->value('');

        if (preg_match($pattern, $value) !== 1) {
            throw new InvalidArgumentException('Conversation input does not match the required pattern.');
        }

        $this->validatedValue = $value;

        return $this;
    }

    public function minLength(int $length): static
    {
        $value = $this->required()->value('');

        if (mb_strlen($value) < $length) {
            throw new InvalidArgumentException("Conversation input must contain at least {$length} characters.");
        }

        $this->validatedValue = $value;

        return $this;
    }

    public function maxLength(int $length): static
    {
        $value = $this->required()->value('');

        if (mb_strlen($value) > $length) {
            throw new InvalidArgumentException("Conversation input must contain no more than {$length} characters.");
        }

        $this->validatedValue = $value;

        return $this;
    }

    public function in(array $allowed): static
    {
        $value = $this->required()->value('');

        if (!in_array($value, $allowed, true)) {
            throw new InvalidArgumentException('Conversation input is not an allowed value.');
        }

        $this->validatedValue = $value;

        return $this;
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
