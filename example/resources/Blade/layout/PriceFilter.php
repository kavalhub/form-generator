@php
/** @var \Kavalhub\FormGenerator\Blade\BladeDecorator $decorator */
/** @var \Kavalhub\FormGenerator\Html\Group $element */
$children = iterator_to_array($element->getAll(), false);
$minEl = $children[0] ?? null;
$maxEl = $children[1] ?? null;
$element->addClass(['demo-price-filter']);
$body = '<div class="demo-price-filter__rows">';
if ($minEl !== null) {
    $body .= '<div class="demo-price-filter__row">'
        . '<span class="demo-price-filter__caption">От</span>'
        . '<span class="js-price-min-label demo-price-label"></span>'
        . $decorator->decorateChild($minEl)->render()
        . '</div>';
}
if ($maxEl !== null) {
    $body .= '<div class="demo-price-filter__row">'
        . '<span class="demo-price-filter__caption">До</span>'
        . '<span class="js-price-max-label demo-price-label"></span>'
        . $decorator->decorateChild($maxEl)->render()
        . '</div>';
}
$body .= '</div>';
$__html = '<' . $element->getTag() . $element->getHtmlTrait(['HtmlName']) . '>'
    . $body
    . '</' . $element->getTag() . '>';
@endphp
{!! $__html !!}
