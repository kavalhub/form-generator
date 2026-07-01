<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Observer\ElementObserverInterface;

class ObserverFacet implements ElementObserverInterface
{
    private array $facetList = [];

    public function __construct(private readonly Storage $storage)
    {
    }

    public function update(ElementInterface $element): void
    {
        $this->facetList = array_merge($this->facetList, $element->getValueArray());
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->storage->getProductIdsByFacets($this->facetList);
    }
}
