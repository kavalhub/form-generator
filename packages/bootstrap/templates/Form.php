<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = (new $this($childElement))->getHtml();
}
$html = implode('', $html);

return '<form' . $this->element->getHtmlTrait() . '>' . $html . '</form>';