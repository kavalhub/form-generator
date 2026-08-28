<?php
declare(strict_types=1);

/** @var int $totalCount */
/** @var list<array{id: string, name: string, category: string, price: string, currency: string, facetsHtml: string}> $rows */
/** @var string $paginatorHtml */
/** @var string $pageMeta */
/** @var bool $empty */
?>
<div class="mt-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="mb-3 flex flex-wrap items-baseline gap-2">
        <strong class="text-slate-900">Результаты (<?= htmlspecialchars((string)$totalCount, ENT_QUOTES) ?>)</strong>
        <?php if ($pageMeta !== ''): ?>
            <span class="text-sm text-slate-500"><?= htmlspecialchars($pageMeta, ENT_QUOTES) ?></span>
        <?php endif; ?>
    </div>
    <?php if ($empty): ?>
        <p class="mb-0 text-sm text-slate-500">Нет товаров по выбранным фильтрам.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="demo-filter-results-table w-full min-w-full border-separate border-spacing-0 text-sm">
                <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <th class="demo-filter-col-id border-b border-slate-200 px-3 py-2">ID</th>
                    <th class="border-b border-slate-200 px-3 py-2">Название</th>
                    <th class="border-b border-slate-200 px-3 py-2">Категория</th>
                    <th class="border-b border-slate-200 px-3 py-2">Цена</th>
                    <th class="demo-filter-col-facets border-b border-slate-200 px-3 py-2">Фасеты</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr class="even:bg-slate-50">
                        <td class="demo-filter-col-id border-b border-slate-100 px-3 py-2 text-slate-700"><?= htmlspecialchars($row['id'], ENT_QUOTES) ?></td>
                        <td class="border-b border-slate-100 px-3 py-2 text-slate-700"><?= htmlspecialchars($row['name'], ENT_QUOTES) ?></td>
                        <td class="border-b border-slate-100 px-3 py-2 text-slate-700"><?= htmlspecialchars($row['category'], ENT_QUOTES) ?></td>
                        <td class="border-b border-slate-100 px-3 py-2 text-slate-700">
                            <?= htmlspecialchars($row['price'], ENT_QUOTES) ?>
                            <?= htmlspecialchars($row['currency'], ENT_QUOTES) ?>
                        </td>
                        <td class="demo-filter-col-facets border-b border-slate-100 px-3 py-2 text-slate-700"><?= $row['facetsHtml'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($paginatorHtml !== ''): ?>
            <div class="demo-filter-pagination mt-3"><?= $paginatorHtml ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
