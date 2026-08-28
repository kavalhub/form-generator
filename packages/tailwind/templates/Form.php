<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}
$html = implode('', $html);

return '<form' . $this->element->getHtmlTrait() . '>' . $html . '</form>';