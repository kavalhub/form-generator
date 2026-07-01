<?php
declare(strict_types=1);

$this->element->addClass(['form-control']);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors()
        . '</div>' : '';

return '<div class="form-group">' . $this->element->getHtml() . $error . '</div>';
