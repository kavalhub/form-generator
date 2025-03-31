<?php
declare(strict_types=1);

/*$errorList = $this->element->getError();
$errorHtml = !empty($errorList) ? '<div class="invalid-feedback">' . implode(
        '<br />',
        $errorList
    ) . '</div>' : '';*/
$this->element->addClass(['form-control', 'form-control-color']);

return '<div class="form-group">' . $this->element->getHtml() . '</div>';
