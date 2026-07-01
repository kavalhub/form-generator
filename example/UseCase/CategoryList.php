<?php
declare(strict_types=1);

namespace Kavalhub\Example\UseCase;

use Kavalhub\Example\Env\Storage;
use Generator;

class CategoryList
{
    private array $where = [];
    private array $params = [];

    public function __construct(private readonly Storage $storage)
    {
    }

    public function addNameFilter(string $name): self
    {
        $this->where[] = 'tc.name = ?';
        $this->params[] = $name;

        return $this;
    }

    public function addRawFilter(string $condition): self
    {
        $this->where[] = $condition;

        return $this;
    }

    public function get(): Generator
    {
        return $this->storage->getCategoryList($this->where, $this->params);
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
