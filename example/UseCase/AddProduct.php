<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Env\Storage;

readonly class AddProduct
{
    public function __construct(private Storage $storage)
    {
    }

    /**
     * @param array<int, string> $facetValues facet_id => value
     */
    public function execute(string $name, int $categoryId, float $price, array $facetValues): int
    {
        return $this->storage->addProduct($name, $categoryId, $price, $facetValues);
    }
}
