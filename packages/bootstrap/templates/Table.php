<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors()
        . '</div>' : '';

$this->element->addClass(['table', 'px-2']);
return '<table' . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</table>' . $error;