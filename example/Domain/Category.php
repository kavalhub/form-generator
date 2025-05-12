<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Domain;

readonly class Category
{
    public function __construct(private string $name, private string $uuid)
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
