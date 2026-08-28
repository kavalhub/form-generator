<?php
declare(strict_types=1);

namespace Kavalhub\Example\Api;

use Kavalhub\Example\Presentation\Web\Form\FacetProductForm;

final class FilterApiPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function present(FacetProductForm $form): array
    {
        $applied = $form->isFiltered()
            ? $form->getAppliedFilters()
            : ['categories' => [], 'facets' => []];
        $products = $form->isFiltered()
            ? array_values(array_map(static function (array $product, int|string $id): array {
                $product['id'] = (int)$id;

                return $product;
            }, $form->getProductList()->__toArray(), array_keys($form->getProductList()->__toArray())))
            : [];

        $facetOptions = [];
        if ($form->isFiltered()) {
            foreach ($form->getProductList()->getFacet() as $name => $facet) {
                $facetOptions[$name] = $facet['value'] ?? [];
            }
        }

        return [
            'applied_filters' => $applied,
            'products' => $products,
            'facet_options' => $facetOptions,
            'total' => count($products),
        ];
    }

    /**
     * @return list<array{id: int, name: string, count: int}>
     */
    public static function presentCategories(FacetProductForm $form): array
    {
        $group = $form->getByName('gc');
        $list = [];
        if ($group->getComposite()) {
            foreach ($group->getComposite()->getAll() as $child) {
                if (!method_exists($child, 'getValue') || !method_exists($child, 'getLabel')) {
                    continue;
                }
                $label = $child->getLabel();
                if (preg_match('/^(.+?)\s-\s(\d+)$/', $label, $matches)) {
                    $list[] = [
                        'id' => (int)$child->getValue(),
                        'name' => $matches[1],
                        'count' => (int)$matches[2],
                    ];
                }
            }
        }

        return $list;
    }
}
