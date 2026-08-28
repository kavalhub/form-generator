<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Layout;

use Kavalhub\Example\Presentation\Http\DecoratorMode;
use Kavalhub\Example\Presentation\Web\Render\FormRenderer;

final class FilterResultsView
{
    /**
     * @param array<int|string, array<string, mixed>> $products
     * @param array{categories: list<string>, facets: array<string, list<string>>} $appliedFilters
     * @return list<array{id: string, name: string, category: string, price: string, currency: string, facetsHtml: string}>
     */
    public static function buildRows(array $products, array $appliedFilters): array
    {
        $rows = [];
        foreach ($products as $id => $product) {
            $rows[] = [
                'id' => (string)$id,
                'name' => (string)($product['name'] ?? ''),
                'category' => (string)($product['category'] ?? ''),
                'price' => (string)($product['price'] ?? ''),
                'currency' => (string)($product['currency'] ?? ''),
                'facetsHtml' => self::formatFacetsHtml(
                    is_array($product['facet'] ?? null) ? $product['facet'] : [],
                    $appliedFilters['facets'] ?? [],
                ),
            ];
        }

        return $rows;
    }

    /**
     * @param array<string, list<string>> $facets
     * @param array<string, list<string>> $appliedFacetFilters
     */
    public static function formatFacetsHtml(array $facets, array $appliedFacetFilters): string
    {
        if ($facets === []) {
            return '<span class="demo-filter-facets-empty">—</span>';
        }

        $parts = [];
        foreach ($facets as $facetName => $values) {
            if (!is_array($values)) {
                continue;
            }
            $activeValues = $appliedFacetFilters[$facetName] ?? [];
            $renderValues = array_map(
                static fn (string $value): string => in_array($value, $activeValues, true)
                    ? '<mark>' . htmlspecialchars($value, ENT_QUOTES) . '</mark>'
                    : htmlspecialchars($value, ENT_QUOTES),
                $values,
            );
            $parts[] = '<span class="demo-filter-facet-item"><strong>'
                . htmlspecialchars((string)$facetName, ENT_QUOTES) . ':</strong> '
                . implode(', ', $renderValues) . '</span>';
        }

        return '<div class="demo-filter-facets">' . implode('', $parts) . '</div>';
    }

    /**
     * @param list<array{id: string, name: string, category: string, price: string, currency: string, facetsHtml: string}> $rows
     */
    public static function renderTable(
        FormRenderer $renderer,
        DecoratorMode $mode,
        int $totalCount,
        array $rows,
        string $paginatorHtml,
        string $pageMeta,
    ): string {
        return $renderer->renderView('layout/FilterResults', $mode, [
            'totalCount' => $totalCount,
            'rows' => $rows,
            'paginatorHtml' => $paginatorHtml,
            'pageMeta' => $pageMeta,
            'empty' => $rows === [],
        ]);
    }
}
