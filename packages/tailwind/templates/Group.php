<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="mt-1 text-sm text-red-600 px-2 mb-2">' . $this->element->getDisplayErrors()
        . '</div>' : '';

$this->element->addClass(['p-3']);
return '<' . $this->element->getTag() . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $this->element->getTag() . '>' . $error;