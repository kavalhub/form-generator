@php
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->render();
}
$html = implode('', $html);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';

$element->addClass(['fg-blade-group']);
$__html = '<' . $element->getTag() . $element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $element->getTag() . '>' . $error;
@endphp
{!! $__html !!}
