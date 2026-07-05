<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Domain\Product;
use Kavalhub\Example\Env\Storage;

readonly class AddProduct
{
    public function __construct(private Storage $storage)
    {
    }

    public function execute(Product $product): Product
    {
        return $this->storage->addProduct($product);
    }
}
