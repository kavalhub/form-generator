<?php
declare(strict_types=1);

$errorHtml = !empty($this->element->getError())
    ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors() . '</div>'
    : '';
$this->element->addClass(['form-check-input']);

return '<div class="form-group"><label for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $this->element->render() . $errorHtml . '</div>';
