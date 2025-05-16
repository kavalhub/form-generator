<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = (new $this($childElement))->getHtml();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . implode('<br>', $this->element->getError())
        . '</div>' : '';

$this->element->addClass(['table', 'px-2']);
return '<table' . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</table>' . $error;