@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->getHtml();
}
$html = implode('', $html);

$element->addClass(['fg-blade-nav']);
$__html = '<' . $element->getTag() . $element->getHtmlTrait() . '>' . $html . '</'
    . $element->getTag() . '>';
@endphp
{!! $__html !!}
