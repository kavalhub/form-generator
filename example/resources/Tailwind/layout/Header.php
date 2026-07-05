<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->getHtml();
}

return '<div class="mb-6">' . implode('', $html) . '</div>';
