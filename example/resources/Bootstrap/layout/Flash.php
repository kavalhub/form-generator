<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}

return '<div class="alert alert-info mb-4">' . implode('', $html) . '</div>';
