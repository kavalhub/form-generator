<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Layout;

use Kavalhub\Example\Presentation\Http\DecoratorMode;
use Kavalhub\Example\Presentation\Web\Form\FacetProductForm;
use Kavalhub\Example\Presentation\Web\Render\FormRenderer;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

final class FilterLiveRegion
{
    public const REGION_ID = 'filter-live-region';

    public function __construct(
        private readonly FormRenderer $renderer,
    ) {
    }

    /**
     * @param callable(ElementInterface): string $renderForm
     */
    public function render(FacetProductForm $form, callable $renderForm, DecoratorMode $mode): string
    {
        $filterApplied = $form->isFiltered();
        $appliedFilters = $filterApplied ? $form->getAppliedFilters() : ['categories' => [], 'facets' => [], 'price' => null];
        $totalCount = 0;
        $filteredProducts = [];
        $paginator = $filterApplied ? $form->getPaginator() : null;

        if ($filterApplied) {
            $totalCount = $form->getProductList()->count();
            if ($paginator !== null) {
                $filteredProducts = $form->getProductList()->toPageArray(
                    $paginator->getLimit(),
                    $paginator->getOffset(),
                );
            } else {
                $filteredProducts = $form->getProductList()->__toArray();
            }
        }

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
                    <?php if ($appliedFilters['price'] !== null): ?>
                        <tr>
                            <th>Цена</th>
                            <td>
                                <?= htmlspecialchars(
                                    number_format($appliedFilters['price']['min'], 0, '', ' ')
                                    . ' – '
                                    . number_format($appliedFilters['price']['max'], 0, '', ' ')
                                    . ' '
                                    . $appliedFilters['price']['currency'],
                                    ENT_QUOTES,
                                ) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
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

        <?php
        $pageMeta = '';
        if ($paginator !== null && $totalCount > 0) {
            $pageMeta = ' — страница ' . $paginator->getPage() . ' из ' . $paginator->getNumPages();
        }
        $paginatorHtml = $paginator !== null && $paginator->getNumPages() > 1
            ? $renderForm($paginator)
            : '';
        echo FilterResultsView::renderTable(
            $this->renderer,
            $mode,
            $totalCount,
            FilterResultsView::buildRows($filteredProducts, $appliedFilters),
            $paginatorHtml,
            $pageMeta,
        );
        ?>
    <?php endif; ?>
</div>
        <?php

        return (string)ob_get_clean();
    }
}
