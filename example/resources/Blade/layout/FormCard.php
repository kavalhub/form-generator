@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->render();
}
$__html = '<div class="fg-blade-card">' . implode('', $html) . '</div>';
@endphp
{!! $__html !!}
