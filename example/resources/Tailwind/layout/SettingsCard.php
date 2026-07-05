<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->getHtml();
}

return '<div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">' . implode('', $html) . '</div>';
