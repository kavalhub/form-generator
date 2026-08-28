<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = '<li class="nav-item">' . $this->decorateChild($childElement)->render() . '</li>';
}

return '<ul class="nav nav-pills mb-4">' . implode('', $html) . '</ul>';
