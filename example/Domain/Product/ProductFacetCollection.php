<?php
declare(strict_types=1);

namespace Kavalhub\Example\Domain\Product;

use SplObjectStorage;

class ProductFacetCollection extends SplObjectStorage
{
    public function add(ProductFacet $object): self
    {
        $this->attach($object);

        return $this;
    }
}