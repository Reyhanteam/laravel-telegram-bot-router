<?php

namespace ReyhanTeam\TelegramBotRouter;

class TelegramRouteRegistrar
{
    public function __construct(protected int $routeIndex)
    {
    }

    public function name(string $name): self
    {
        TelegramBot::nameRoute($this->routeIndex, $name);
        return $this;
    }

    public function where(string $name, string $expression): self
    {
        TelegramBot::addConstraint($this->routeIndex, $name, $expression);
        return $this;
    }

    public function whereNumber(string $name): self { return $this->where($name, '\\d+'); }
    public function whereAlpha(string $name): self { return $this->where($name, '[A-Za-z]+'); }
    public function whereAlphaNumeric(string $name): self { return $this->where($name, '[A-Za-z0-9]+'); }

    public function whereIn(string $name, array $values): self
    {
        $escaped = array_map(static fn ($value): string => preg_quote((string) $value, '/'), $values);
        return $this->where($name, '(?:'.implode('|', $escaped).')');
    }

    /** Allow this route only for the specified Telegram user IDs. */
    public function whereUser(int|string|array $userIds): self
    {
        TelegramBot::addUserCondition($this->routeIndex, $userIds);
        return $this;
    }

    /** Alias for whereUser(). */
    public function user(int|string|array $userIds): self { return $this->whereUser($userIds); }

    /** Allow this route only for Telegram chat administrators/owners. */
    public function adminOnly(): self
    {
        TelegramBot::enableAdminOnly($this->routeIndex);
        return $this;
    }

    /** Allow this route only in a private chat. */
    public function privateChat(): self { return $this->chatType('private'); }

    /** Allow this route only in a group or supergroup. */
    public function groupChat(): self { return $this->chatType(['group', 'supergroup']); }

    /** Allow this route only in a channel. */
    public function channel(): self { return $this->chatType('channel'); }

    /** Restrict the route to one or more Telegram chat types. */
    public function chatType(string|array $types): self
    {
        TelegramBot::addChatTypeCondition($this->routeIndex, $types);
        return $this;
    }

    /** Require one or more Telegram administrator permission fields. */
    public function userPermission(string|array $permissions): self
    {
        TelegramBot::addPermissionCondition($this->routeIndex, $permissions);
        return $this;
    }

    /** Alias for userPermission(). */
    public function permission(string|array $permissions): self { return $this->userPermission($permissions); }

    public function rateLimit(string $scope, int $maxAttempts, int $decaySeconds = 60): self
    {
        TelegramBot::addRateLimit($this->routeIndex, $scope, $maxAttempts, $decaySeconds);
        return $this;
    }

    public function rateLimits(array $limits): self
    {
        TelegramBot::addRateLimits($this->routeIndex, $limits);
        return $this;
    }

    public function queue(?string $queue = null): self
    {
        TelegramBot::enableQueue($this->routeIndex, $queue);
        return $this;
    }
}
