@php
$children = iterator_to_array($element->getAll(), false);
$legend = '';
$body = '';
if ($children !== []) {
    $first = array_shift($children);
    $legend = method_exists($first, 'getLabel') ? $first->getLabel() : '';
    if ($legend === '' && $first instanceof \Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface) {
        $legend = trim(strip_tags($first->render()));
    }
    foreach ($children as $child) {
        $body .= '<span class="fg-blade-inline-option">' . $decorator->decorateChild($child)->render() . '</span>';
    }
}
$__html = '<fieldset class="fg-blade-fieldset"><legend class="fg-blade-legend">'
    . e($legend) . '</legend><div class="fg-blade-fieldset-options">'
    . $body . '</div></fieldset>';
@endphp
{!! $__html !!}
