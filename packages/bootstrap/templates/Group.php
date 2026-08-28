<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback px-2 mb2">' . $this->element->getDisplayErrors()
        . '</div>' : '';

$this->element->addClass(['px-2']);
return '<' . $this->element->getTag() . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $this->element->getTag() . '>' . $error;