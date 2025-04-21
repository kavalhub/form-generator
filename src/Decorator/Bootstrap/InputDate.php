<?php
declare(strict_types=1);

$this->element->addClass([
    'form-control',
]);

return '<div class="form-group">' . $this->element->getHtml() . '</div>';
