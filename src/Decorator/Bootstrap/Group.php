<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = (new $this($childElement))->getHtml();
}
$html = implode('', $html);
//$this->element->addClass(['form-control']);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . implode('<br>', $this->element->getError())
        . '</div>' : '';

return '<div' . $this->element->getHtmlTrait() . '>' . $html . '</div>' . $error;
