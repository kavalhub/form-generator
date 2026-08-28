<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}

return '<div class="card shadow-sm mb-3"><div class="card-body"><form'
    . $this->element->getHtmlTrait()
    . '>'
    . implode('', $html)
    . '</form></div></div>';
