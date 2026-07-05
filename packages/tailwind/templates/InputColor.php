<?php
declare(strict_types=1);

$this->element->addClass([
    'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200',
    'h-10 w-14 cursor-pointer rounded border border-slate-300 p-1'
]);

return '<div class="mb-3 space-y-1">' . $this->element->render() . '</div>';
