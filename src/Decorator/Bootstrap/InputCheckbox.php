<?php
declare(strict_types=1);

$this->element->addClass([
    'form-check-input',
]);

return '<div class="form-check form-switch">' . $this->element->getHtml() . $this->element->getLabel() . '</div>';
