<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Env\Storage;
use Generator;

readonly class CategoryList
{
    public function __construct(private Storage $storage)
    {
    }

    public function get(): Generator
    {
        return $this->storage->getCategoryList();
    }

    public function __toArray(): array
    {
        $array = [];
        foreach ($this->storage->getCategoryList() as $category) {
            $array[] = $category;
        }

        return $array;
    }
}