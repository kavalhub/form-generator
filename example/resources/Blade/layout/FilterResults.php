<?php
declare(strict_types=1);

/** @var int $totalCount */
/** @var list<array{id: string, name: string, category: string, price: string, currency: string, facetsHtml: string}> $rows */
/** @var string $paginatorHtml */
/** @var string $pageMeta */
/** @var bool $empty */
?>
<div class="fg-blade-card mt-4">
    <div class="demo-filter-results-header">
        <strong>Результаты (<?= htmlspecialchars((string)$totalCount, ENT_QUOTES) ?>)</strong>
        <?php if ($pageMeta !== ''): ?>
            <span class="demo-filter-results-meta"><?= htmlspecialchars($pageMeta, ENT_QUOTES) ?></span>
        <?php endif; ?>
    </div>
    <?php if ($empty): ?>
        <p class="demo-filter-empty">Нет товаров по выбранным фильтрам.</p>
    <?php else: ?>
        <div class="demo-filter-table-wrap">
            <table class="fg-blade-table demo-filter-results-table demo-filter-results-table--striped">
                <thead>
                <tr>
                    <th class="demo-filter-col-id">ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th class="demo-filter-col-facets">Фасеты</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="demo-filter-col-id"><?= htmlspecialchars($row['id'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['name'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['category'], ENT_QUOTES) ?></td>
                        <td>
                            <?= htmlspecialchars($row['price'], ENT_QUOTES) ?>
                            <?= htmlspecialchars($row['currency'], ENT_QUOTES) ?>
                        </td>
                        <td class="demo-filter-col-facets"><?= $row['facetsHtml'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($paginatorHtml !== ''): ?>
            <div class="demo-filter-pagination"><?= $paginatorHtml ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
