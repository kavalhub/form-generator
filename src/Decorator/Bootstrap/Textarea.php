<?php
declare(strict_types=1);

$errorList = $this->element->getError();
$errorHtml = !empty($errorList) ? '<div class="invalid-feedback">' . implode(
        '<br />',
        $errorList
    ) . '</div>' : '';
$this->element->addClass(['form-control']);

return '<div class="form-group"><label class="form-group required" for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $this->element->getHtml() . $errorHtml . '</div>';
