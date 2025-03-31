<?php
declare(strict_types=1);

$errorList = $this->element->getError();
$errorHtml = !empty($errorList) ? '<div class="invalid-feedback">' . implode(
        '<br />',
        $errorList
    ) . '</div>' : '';
$this->element->addClass(['form-control']);
$html = '';
foreach ($this->element->getAll() as $childElement) {
    $html .= (new $this($childElement))->getHtml();
}

return '<div class="form-group"><label for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $html . $errorHtml . '</div>';
