<?php
declare(strict_types=1);

$this->element->addClass([
    'h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500',
]);

return '<label class="flex items-center gap-2" for="' . htmlspecialchars($this->element->getId(), ENT_QUOTES) . '">'
    . $this->element->renderControl()
    . '<span class="text-sm text-slate-800">' . htmlspecialchars($this->element->getLabel(), ENT_QUOTES) . '</span>'
    . '</label>';
