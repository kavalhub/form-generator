<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Domain;

readonly class Facet
{
    public function __construct(private string $name, private string $value, private ?string $uuid = null)
    {
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
