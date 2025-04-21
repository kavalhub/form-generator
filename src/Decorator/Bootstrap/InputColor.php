<?php
declare(strict_types=1);

$this->element->addClass([
    'form-control',
    'form-control-color'
]);

return '<div class="form-group">' . $this->element->getHtml() . '</div>';
