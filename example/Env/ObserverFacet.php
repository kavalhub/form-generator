<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Observer\ElementObserverInterface;

class ObserverFacet implements ElementObserverInterface
{
    private array $facetList = [];

    public function update(ElementInterface $element)
    {
        $this->facetList = array_merge($this->facetList, $element->getValueArray());
    }

    public function getQuery(): string
    {
        if (empty($this->facetList)) {
            return '';
        }
        $where = [];
        foreach ($this->facetList as $name => $facet) {
            $where[] = "(f.name = '" . $name . "' AND pf.value IN ('" . implode("','", $facet) . "'))";
        }
        $query = "SELECT pf.product_id
                    FROM dks_slim.temp_product_facet pf
                        JOIN dks_slim.temp_facet f ON pf.facet_id = f.id
                    WHERE " . implode(' OR ', $where) . "
                    GROUP BY pf.product_id
                    HAVING COUNT(DISTINCT f.name) = " . count($this->facetList);
        return $query;
    }
}