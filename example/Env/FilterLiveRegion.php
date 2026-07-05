<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

final class FilterLiveRegion
{
    public const REGION_ID = 'filter-live-region';

    /**
     * @param callable(ElementInterface): string $renderForm
     */
    public static function render(FacetProductForm $form, callable $renderForm): string
    {
        $filterApplied = $form->isFiltered();
        $appliedFilters = $filterApplied ? $form->getAppliedFilters() : ['categories' => [], 'facets' => []];
        $filteredProducts = $filterApplied ? $form->getProductList()->__toArray() : [];

        ob_start();
        ?>
<div id="<?= self::REGION_ID ?>">
    <div class="card shadow-sm">
        <div class="card-body">
            <?= $renderForm($form) ?>
        </div>
    </div>
    <?php if ($filterApplied): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header">Активные фильтры</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                    <tr>
                        <th style="width: 140px">Категории</th>
                        <td>
                            <?= $appliedFilters['categories'] !== []
                                ? htmlspecialchars(implode(', ', $appliedFilters['categories']), ENT_QUOTES)
                                : '—' ?>
                        </td>
                    </tr>
                    <?php foreach ($appliedFilters['facets'] as $facetName => $values): ?>
                        <tr>
                            <th><?= htmlspecialchars((string)$facetName, ENT_QUOTES) ?></th>
                            <td><?= htmlspecialchars(implode(', ', $values), ENT_QUOTES) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($appliedFilters['facets'] === []): ?>
                        <tr>
                            <th>Фасеты</th>
                            <td class="text-muted">не выбраны</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">Результаты (<?= count($filteredProducts) ?>)</div>
            <div class="card-body">
                <?php if ($filteredProducts === []): ?>
                    <p class="text-muted mb-0">Нет товаров по выбранным фильтрам.</p>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>Фасеты</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($filteredProducts as $id => $product): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$id, ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars((string)$product['name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars((string)($product['category'] ?? ''), ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars((string)($product['price'] ?? ''), ENT_QUOTES) ?>
                                    <?= htmlspecialchars((string)($product['currency'] ?? ''), ENT_QUOTES) ?></td>
                                <td>
                                    <?php foreach ($product['facet'] ?? [] as $facetName => $values): ?>
                                        <div>
                                            <strong><?= htmlspecialchars($facetName, ENT_QUOTES) ?>:</strong>
                                            <?php
                                            $activeValues = $appliedFilters['facets'][$facetName] ?? [];
                                            $renderValues = array_map(
                                                static fn (string $value): string => in_array($value, $activeValues, true)
                                                    ? '<mark>' . htmlspecialchars($value, ENT_QUOTES) . '</mark>'
                                                    : htmlspecialchars($value, ENT_QUOTES),
                                                $values,
                                            );
                                            echo implode(', ', $renderValues);
                                            ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
        <?php

        return (string)ob_get_clean();
    }
}
