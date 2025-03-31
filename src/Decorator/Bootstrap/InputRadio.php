<?php
declare(strict_types=1);

$errorList = $this->element->getError();
$errorHtml = !empty($errorList) ? '<div class="invalid-feedback">' . implode(
        '<br />',
        $errorList
    ) . '</div>' : '';
$this->element->addClass(['form-check-input']);

return '<div class="form-check form-switch">' . $this->element->getHtml() . '<label for="' . $this->element->getId()
    . '">' . $this->element->getTitle() . '</label></div>';
