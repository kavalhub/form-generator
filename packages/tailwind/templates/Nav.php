<?php
declare(strict_types=1);

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}
$html = implode('', $html);

$this->element->addClass(['flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3']);
return '<' . $this->element->getTag() . $this->element->getHtmlTrait() . '>' . $html . '</'
    . $this->element->getTag() . '>';