<?php
declare(strict_types=1);

$this->element->addClass(['form-select']);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors()
        . '</div>' : '';

return '<div class="form-group me-2">' . $this->element->render() . $error . '</div>';
