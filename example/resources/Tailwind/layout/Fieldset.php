<?php
declare(strict_types=1);

$children = iterator_to_array($this->element->getAll(), false);
$legend = '';
$body = '';
if ($children !== []) {
    $first = array_shift($children);
    $legend = method_exists($first, 'getLabel') ? $first->getLabel() : '';
    if ($legend === '' && $first instanceof \Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface) {
        $legend = trim(strip_tags($first->render()));
    }
    foreach ($children as $child) {
        $body .= $this->decorateChild($child)->getHtml();
    }
}

return '<fieldset class="mb-4"><legend class="mb-2 block text-sm font-semibold text-slate-800">'
    . htmlspecialchars($legend, ENT_QUOTES) . '</legend><div class="flex flex-wrap gap-4">'
    . $body . '</div></fieldset>';
