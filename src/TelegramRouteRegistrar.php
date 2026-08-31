<?php

namespace ReyhanTeam\TelegramBotRouter;

class TelegramRouteRegistrar
{
    public function __construct(protected int $routeIndex)
    {
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
}
