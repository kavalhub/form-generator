<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = (new $this($childElement))->getHtml();
}
$html = implode('', $html);

$this->element->addClass(['nav', 'px-2']);
return '<' . $this->element->getTag() . $this->element->getHtmlTrait() . '>' . $html . '</'
    . $this->element->getTag() . '>';