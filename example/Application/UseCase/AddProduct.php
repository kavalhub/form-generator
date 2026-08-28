<?php
declare(strict_types=1);

namespace Kavalhub\Example\Application\UseCase;

use Kavalhub\Example\Domain\Product;
use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;

readonly class AddProduct
{
    public function __construct(private CatalogRepositoryInterface $repository)
    {
    }

    public function execute(Product $product): Product
    {
        return $this->repository->addProduct($product);
    }
}
