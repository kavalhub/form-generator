<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Domain;

readonly class Product
{
    public function __construct(
        private string $name, private float $price, private Category $category, private FacetCollection $facets,
        private ?string $uuid = null
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getFacets(): FacetCollection
    {
        return $this->facets;
    }
}
