@php
$element->addClass(['fg-blade-form']);
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->render();
}
$html = implode('', $html);
$__html = '<form' . $element->getHtmlTrait() . '>' . $html . '</form>';
@endphp
{!! $__html !!}
