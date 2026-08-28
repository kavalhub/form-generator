<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}

return '<div class="d-flex flex-wrap gap-2 mb-4 align-items-center">' . implode('', $html)
    . '<span id="seed-status" class="text-muted small"></span></div>';
