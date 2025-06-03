<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Env\Storage;
use Generator;

class CategoryList
{
    private array $filter = [];

    public function __construct(private readonly Storage $storage)
    {
    }

    public function addFilter(array $filter): self
    {
        $this->filter = array_merge($this->filter, $filter);

        return $this;
    }

    public function get(): Generator
    {
        return $this->storage->getCategoryList(!empty($this->filter) ? 'WHERE ' . implode(' AND ', $this->filter) : '');
    }

    public function __toArray(): array
    {
        $array = [];
        foreach ($this->get() as $category) {
            $array[] = $category;
        }

        return $array;
    }
}