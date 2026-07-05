<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Observer\ElementObserverInterface;

class ObserverFacet implements ElementObserverInterface
{
    /** @var array<string, string[]> */
    private array $facetList = [];

    public function __construct(private readonly Storage $storage)
    {
    }

    public function reset(): void
    {
        $this->facetList = [];
    }

    public function update(ElementInterface $element): void
    {
        $this->facetList = array_merge($this->facetList, $element->getValueArray());
    }

    /**
     * @return array<string, string[]>
     */
    public function getFacetList(): array
    {
        return $this->facetList;
    }

    public function hasSelection(): bool
    {
        return $this->facetList !== [];
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->storage->getProductIdsByFacets($this->facetList);
    }
}
