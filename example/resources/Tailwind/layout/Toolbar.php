<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}

return '<div class="mb-6 flex flex-wrap items-center gap-2">' . implode('', $html)
    . '<span id="seed-status" class="text-sm text-slate-500"></span></div>';
