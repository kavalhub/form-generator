@php
if ($element->isRequired()) {
    $element->addClass(['required']);
}
$element->addClass(['fg-blade-label']);
$__html = $element->render();
@endphp
{!! $__html !!}
