<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Form;

use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Kavalhub\FormGenerator\Event\ElementChangedEvent;

final class FacetSelectionListener
{
    /** @var array<string, string[]> */
    private array $facetList = [];

    public function __construct(private readonly CatalogRepositoryInterface $repository)
    {
    }

    public function reset(): void
    {
        $this->facetList = [];
    }

    public function onElementChanged(ElementChangedEvent $event): void
    {
        $element = $event->getElement();
        if (!method_exists($element, 'getName')) {
            return;
        }
        $name = $element->getName();
        if (!str_starts_with($name, 'g') || $name === 'gc') {
            return;
        }

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
        return $this->repository->getProductIdsByFacets($this->facetList);
    }
}
