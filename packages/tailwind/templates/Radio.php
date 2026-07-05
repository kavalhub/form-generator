<?php
declare(strict_types=1);

$errorHtml = !empty($this->element->getError())
    ? '<div class="mt-1 text-sm text-red-600">' . $this->element->getDisplayErrors() . '</div>'
    : '';
$this->element->addClass(['w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200']);
$html = '';
foreach ($this->element->getAll() as $childElement) {
    $html .= $this->decorateChild($childElement)->getHtml();
}

return '<div class="mb-3"><label for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $html . $errorHtml . '</div>';
