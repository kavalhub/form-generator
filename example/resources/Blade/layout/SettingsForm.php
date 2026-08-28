@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->render();
}
$__html = '<div class="fg-blade-card"><form' . $element->getHtmlTrait() . '>' . implode('', $html) . '</form></div>';
@endphp
{!! $__html !!}
