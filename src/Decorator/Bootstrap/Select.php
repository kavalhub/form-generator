<?php
declare(strict_types=1);

$errorList = $this->element->getError();
$errorHtml = !empty($errorList) ? '<div class="invalid-feedback">' . implode(
        '<br />',
        $errorList
    ) . '</div>' : '';
$this->element->addClass(['form-select']);

return '<div class="form-group"><label for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $this->element->getHtml() . $errorHtml . '</div>';
