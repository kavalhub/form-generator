<?php
declare(strict_types=1);

/** @var int $totalCount */
/** @var list<array{id: string, name: string, category: string, price: string, currency: string, facetsHtml: string}> $rows */
/** @var string $paginatorHtml */
/** @var string $pageMeta */
/** @var bool $empty */
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        Результаты (<?= htmlspecialchars((string)$totalCount, ENT_QUOTES) ?>)
        <?php if ($pageMeta !== ''): ?>
            <span class="text-muted fw-normal"><?= htmlspecialchars($pageMeta, ENT_QUOTES) ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if ($empty): ?>
            <p class="text-muted mb-0">Нет товаров по выбранным фильтрам.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped demo-filter-results-table mb-0">
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
                <div class="mt-3 demo-filter-pagination"><?= $paginatorHtml ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
