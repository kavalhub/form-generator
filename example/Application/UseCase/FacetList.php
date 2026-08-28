<?php
declare(strict_types=1);

namespace Kavalhub\Example\Application\UseCase;

use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Generator;

class FacetList
{
    private array $where = [];
    private array $params = [];

    public function __construct(private readonly CatalogRepositoryInterface $repository)
    {
    }

    public function addNameFilter(string $name): self
    {
        $this->where[] = 'tf.name = ?';
        $this->params[] = $name;

        return $this;
    }

    public function get(): Generator
    {
        return $this->repository->getFacetList($this->where, $this->params);
    }

    public function __toArray(): array
    {
        $array = [];
        foreach ($this->get() as $facet) {
            $array[] = $facet;
        }

        return $array;
    }
}
