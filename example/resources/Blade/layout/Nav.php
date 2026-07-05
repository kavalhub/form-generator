@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->getHtml();
}
$__html = '<nav class="fg-blade-nav">' . implode('', $html) . '</nav>';
@endphp
{!! $__html !!}
