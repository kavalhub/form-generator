<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}

return '<div class="card shadow-sm"><div class="card-body">' . implode('', $html) . '</div></div>';
