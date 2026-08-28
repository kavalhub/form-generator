@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->render();
}
$html = implode('', $html);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';

$element->addClass(['fg-blade-table']);
$__html = '<table' . $element->getHtmlTrait(['HtmlName']) . '>' . $html . '</table>' . $error;
@endphp
{!! $__html !!}
