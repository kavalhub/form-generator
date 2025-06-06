<?php
declare(strict_types=1);

namespace Kavalhub\Example\Domain;

use Kavalhub\Example\Domain\Product\ProductFacetCollection;

readonly class Product
{
    public function __construct(
        private string $name, private float $price, private Category $category, private ProductFacetCollection $facets,
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

    public function getFacets(): ProductFacetCollection
    {
        return $this->facets;
    }
}
