<?php
declare(strict_types=1);

namespace Kavalhub\Example\Domain\Product;

readonly class ProductFacet
{
    public function __construct(private string $name, private string $value)
    {
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
