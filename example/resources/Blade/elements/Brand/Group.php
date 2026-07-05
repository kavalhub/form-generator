@php
$templateFile = 'resources/Blade/elements/Brand/Group.php';
$html = [];
foreach ($element->getAll() as $childElement) {
    $html[] = $decorator->decorateChild($childElement)->getHtml();
}
$html = implode('', $html);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';
$element->addClass(['fg-blade-group']);
$__html = '<div class="fg-blade-group fg-blade-group--accent-danger">'
    . '<div class="fg-blade-template-meta"><span class="fg-blade-badge fg-blade-badge--danger">Кастомный шаблон</span>'
    . '<span class="fg-blade-template-path">' . e($templateFile) . '</span></div>'
    . '<' . $element->getTag() . $element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $element->getTag() . '>' . $error
    . '</div>';
@endphp
{!! $__html !!}
