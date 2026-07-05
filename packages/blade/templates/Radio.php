@php
$errorHtml = !empty($element->getError())
    ? '<div class="fg-blade-error">' . $element->getDisplayErrors() . '</div>'
    : '';
$html = '';
foreach ($element->getAll() as $childElement) {
    $html .= $decorator->decorateChild($childElement)->getHtml();
}
$__html = '<div class="fg-blade-field fg-blade-group">'
    . '<div class="fg-blade-label">' . $element->getTitle() . '</div>'
    . $html . $errorHtml . '</div>';
@endphp
{!! $__html !!}
