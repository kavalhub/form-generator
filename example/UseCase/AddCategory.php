<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\UseCase;

use Kavalhub\FormGenerator\Domain\Category;
use Kavalhub\FormGenerator\Env\Storage;

readonly class AddCategory
{
    public function __construct(private Storage $storage)
    {
    }

    public function execute(Category $category): bool
    {
        return $this->storage->addCategory($category);
    }
}