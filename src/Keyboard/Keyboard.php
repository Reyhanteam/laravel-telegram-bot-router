<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Keyboard;

use Closure;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Fluent Telegram reply_markup builder.
 *
 * The builder is framework-independent. Call toArray() before passing it to
 * a Telegram API method that has an array-typed reply_markup parameter.
 */
final class Keyboard implements JsonSerializable, Stringable
{
    private function __construct(
        private readonly bool $inline,
        private array $rows = [],
        private array $options = [],
    ) {
    }

    public static function inline(): self
    {
        return new self(true);
    }

    public static function reply(): self
    {
        return new self(false);
    }

    public static function factory(Closure $factory): self
    {
        $keyboard = self::inline();
        $result = $factory($keyboard);

        if (!$result instanceof self) {
            throw new InvalidArgumentException('Keyboard factory must return a Keyboard instance.');
        }

        return $result;
    }

    public static function callbackData(string $route, array $parameters = []): string
    {
        $route = trim($route);

        if ($route === '') {
            throw new InvalidArgumentException('Callback route cannot be empty.');
        }

        if ($parameters === []) {
            self::assertCallbackDataValue($route);
            return $route;
        }

        $pairs = [];
        foreach ($parameters as $key => $value) {
            if (!is_scalar($value) && $value !== null) {
                throw new InvalidArgumentException('Callback data parameters must be scalar or null.');
            }

            $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }

        $data = $route . '?' . implode('&', $pairs);
        self::assertCallbackDataValue($data);

        return $data;
    }

    public function button(string $text, ?string $value = null): self
    {
        $this->assertText($text);

        if ($value === null) {
            return $this->addButton(['text' => $text]);
        }

        if ($this->inline && filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return $this->url($text, $value);
        }

        if (!$this->inline) {
            throw new InvalidArgumentException('Reply keyboard buttons do not accept a callback or URL value.');
        }

        return $this->callbackButton($text, $value);
    }

    public function callbackButton(string $text, string $data): self
    {
        $this->assertInline();
        $this->assertText($text);
        self::assertCallbackDataValue($data);

        return $this->addButton(['text' => $text, 'callback_data' => $data]);
    }

    public function url(string $text, string $url): self
    {
        $this->assertInline();
        $this->assertText($text);

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Inline keyboard URL must be a valid URL.');
        }

        return $this->addButton(['text' => $text, 'url' => $url]);
    }

    public function webApp(string $text, string $url): self
    {
        $this->assertInline();
        $this->assertText($text);

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Web App URL must be a valid URL.');
        }

        return $this->addButton(['text' => $text, 'web_app' => ['url' => $url]]);
    }

    public function login(string $text, string $url, ?string $forwardText = null, ?string $botUsername = null, ?bool $requestWriteAccess = null): self
    {
        $this->assertInline();
        $this->assertText($text);

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Login URL must be a valid URL.');
        }

        $login = ['url' => $url];
        foreach (['forward_text' => $forwardText, 'bot_username' => $botUsername, 'request_write_access' => $requestWriteAccess] as $key => $value) {
            if ($value !== null) {
                $login[$key] = $value;
            }
        }

        return $this->addButton(['text' => $text, 'login_url' => $login]);
    }

    public function switchInlineQuery(string $text, string $query = ''): self
    {
        return $this->addInlineQueryButton($text, 'switch_inline_query', $query);
    }

    public function switchInlineQueryCurrentChat(string $text, string $query = ''): self
    {
        return $this->addInlineQueryButton($text, 'switch_inline_query_current_chat', $query);
    }

    public function pay(string $text): self
    {
        $this->assertInline();
        $this->assertText($text);

        return $this->addButton(['text' => $text, 'pay' => true]);
    }

    public function row(): self
    {
        if ($this->rows === [] || end($this->rows) !== []) {
            $this->rows[] = [];
        }

        return $this;
    }

    public function when(bool $condition, callable $callback): self
    {
        if ($condition) {
            $result = $callback($this);
            if ($result instanceof self && $result !== $this) {
                $this->rows = $result->rows;
                $this->options = $result->options;
            }
        }

        return $this;
    }

    public function unless(bool $condition, callable $callback): self
    {
        return $this->when(!$condition, $callback);
    }

    public function resize(bool $enabled = true): self
    {
        $this->assertReply();
        $this->options['resize_keyboard'] = $enabled;
        return $this;
    }

    public function persistent(bool $enabled = true): self
    {
        $this->assertReply();
        $this->options['is_persistent'] = $enabled;
        return $this;
    }

    public function oneTime(bool $enabled = true): self
    {
        $this->assertReply();
        $this->options['one_time_keyboard'] = $enabled;
        return $this;
    }

    public function selective(bool $enabled = true): self
    {
        $this->assertReply();
        $this->options['selective'] = $enabled;
        return $this;
    }

    public function placeholder(string $text): self
    {
        $this->assertReply();

        if ($text === '' || self::characterLength($text) > 64) {
            throw new InvalidArgumentException('Reply keyboard placeholder must be 1 to 64 characters.');
        }

        $this->options['input_field_placeholder'] = $text;
        return $this;
    }

    public function remove(): self
    {
        $this->assertReply();
        $this->rows = [];
        $this->options = ['remove_keyboard' => true];
        return $this;
    }

    public function forceReply(): self
    {
        $this->assertReply();
        $this->rows = [];
        $this->options = ['force_reply' => true];
        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $this->validate();

        if (($this->options['remove_keyboard'] ?? false) === true || ($this->options['force_reply'] ?? false) === true) {
            return $this->options;
        }

        return $this->inline
            ? ['inline_keyboard' => $this->rows]
            : array_merge(['keyboard' => $this->rows], $this->options);
    }

    public function validate(): self
    {
        $isSpecialReplyMarkup = ($this->options['remove_keyboard'] ?? false) === true
            || ($this->options['force_reply'] ?? false) === true;

        if ($this->rows === [] && !$isSpecialReplyMarkup) {
            throw new InvalidArgumentException('Keyboard must contain at least one button row.');
        }

        foreach ($this->rows as $row) {
            if ($row === []) {
                throw new InvalidArgumentException('Keyboard rows cannot be empty.');
            }

            foreach ($row as $button) {
                if (!isset($button['text'])) {
                    throw new InvalidArgumentException('Keyboard buttons require text.');
                }

                if ($this->inline) {
                    $actions = array_intersect(array_keys($button), [
                        'callback_data',
                        'url',
                        'web_app',
                        'login_url',
                        'switch_inline_query',
                        'switch_inline_query_current_chat',
                        'pay',
                    ]);

                    if (count($actions) !== 1) {
                        throw new InvalidArgumentException('Inline buttons must contain exactly one action.');
                    }

                    if (isset($button['callback_data'])) {
                        self::assertCallbackDataValue($button['callback_data']);
                    }
                }
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    private function addButton(array $button): self
    {
        if ($this->rows === []) {
            $this->rows[] = [];
        }

        $this->rows[array_key_last($this->rows)][] = $button;
        return $this;
    }

    private function addInlineQueryButton(string $text, string $key, string $query): self
    {
        $this->assertInline();
        $this->assertText($text);
        return $this->addButton(['text' => $text, $key => $query]);
    }

    private function assertInline(): void
    {
        if (!$this->inline) {
            throw new InvalidArgumentException('This button is available only on inline keyboards.');
        }
    }

    private function assertReply(): void
    {
        if ($this->inline) {
            throw new InvalidArgumentException('This option is available only on reply keyboards.');
        }
    }

    private function assertText(string $text): void
    {
        if ($text === '' || self::characterLength($text) > 64) {
            throw new InvalidArgumentException('Keyboard button text must be 1 to 64 characters.');
        }
    }

    private static function assertCallbackDataValue(string $data): void
    {
        $length = strlen($data);
        if ($length === 0 || $length > 64) {
            throw new InvalidArgumentException('Callback data must be between 1 and 64 bytes.');
        }
    }

    private static function characterLength(string $value): int
    {
        preg_match_all('/./us', $value, $matches);
        return count($matches[0]);
    }
}
