@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->getHtml();
}
$__html = '<div class="fg-blade-header mb-3">' . implode('', $html) . '</div>';
@endphp
{!! $__html !!}
