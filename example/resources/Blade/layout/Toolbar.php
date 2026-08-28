@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->render();
}
$__html = '<div class="fg-blade-toolbar mb-4">' . implode('', $html)
    . '<span id="seed-status" class="demo-seed-status"></span></div>';
@endphp
{!! $__html !!}
