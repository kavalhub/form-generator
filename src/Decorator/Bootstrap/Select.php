<?php
declare(strict_types=1);

$this->element->addClass(['form-select']);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . implode('<br>', $this->element->getError())
        . '</div>' : '';

return '<div class="form-group me-2">' . $this->element->getHtml() . $error . '</div>';
