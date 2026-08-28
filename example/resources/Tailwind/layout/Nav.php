<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}

return '<nav class="mb-6 flex flex-wrap gap-2">' . implode('', $html) . '</nav>';
