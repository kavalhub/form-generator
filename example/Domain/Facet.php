<?php
declare(strict_types=1);

namespace Kavalhub\Example\Domain;

readonly class Facet
{
    public function __construct(
        private string $name,
        private ?string $uuid = null,
        private string $element = 'InputCheckbox',
    ) {
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getElement(): string
    {
        return $this->element;
    }
}
