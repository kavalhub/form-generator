<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->getHtml();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="mt-1 text-sm text-red-600">' . $this->element->getDisplayErrors()
        . '</div>' : '';

$this->element->addClass(['w-full border-collapse overflow-hidden rounded-xl border border-slate-200 text-sm']);
return '<table' . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</table>' . $error;