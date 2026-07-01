<?php
declare(strict_types=1);

$errorHtml = !empty($this->element->getError())
    ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors() . '</div>'
    : '';
$this->element->addClass(['form-control']);
$html = '';
foreach ($this->element->getAll() as $childElement) {
    $html .= (new $this($childElement))->getHtml();
}

return '<div class="form-group"><label for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $html . $errorHtml . '</div>';
