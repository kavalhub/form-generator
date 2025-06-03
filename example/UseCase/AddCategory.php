<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Env\Storage;

readonly class AddCategory
{
    public function __construct(private Storage $storage)
    {
    }

    public function execute(Category $category): Category
    {
        return $this->storage->addCategory($category);
    }
}