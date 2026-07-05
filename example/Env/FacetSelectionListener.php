<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Event\ElementChangedEvent;

final class FacetSelectionListener
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

    public function onElementChanged(ElementChangedEvent $event): void
    {
        $this->facetList = array_merge($this->facetList, $event->getElement()->getValueArray());
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
        foreach ($this->facetList as $values) {
            if ($values !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->storage->getProductIdsByFacets($this->facetList);
    }
}
