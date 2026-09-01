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

    public function whereNumber(string $name): self
    {
        return $this->where($name, '\\d+');
    }

    public function whereAlpha(string $name): self
    {
        return $this->where($name, '[A-Za-z]+');
    }

    public function whereAlphaNumeric(string $name): self
    {
        return $this->where($name, '[A-Za-z0-9]+');
    }

    public function whereIn(string $name, array $values): self
    {
        $escaped = array_map(
            static fn ($value): string => preg_quote((string) $value, '/'),
            $values
        );

        return $this->where($name, '(?:'.implode('|', $escaped).')');
    }

    public function rateLimit(string $scope, int $maxAttempts, int $decaySeconds = 60): self
    {
        TelegramBot::addRateLimit(
            $this->routeIndex,
            $scope,
            $maxAttempts,
            $decaySeconds
        );

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
