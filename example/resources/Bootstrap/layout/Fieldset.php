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
        $body .= '<div class="form-check form-check-inline">'
            . $this->decorateChild($child)->render() . '</div>';
    }
}

return '<fieldset class="mb-3"><legend class="col-form-label pt-0">'
    . htmlspecialchars($legend, ENT_QUOTES) . '</legend>' . $body . '</fieldset>';
