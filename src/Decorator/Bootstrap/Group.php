<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = (new $this($childElement))->getHtml();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback px-2 mb2">' . implode('<br>', $this->element->getError())
        . '</div>' : '';

$this->element->addClass(['px-2']);
return '<' . $this->element->getTag() . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $this->element->getTag() . '>' . $error;