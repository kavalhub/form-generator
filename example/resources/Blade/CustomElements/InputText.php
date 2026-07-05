@php
$templateFile = 'resources/Blade/CustomElements/InputText.php';
$element->addClass(['fg-blade-input']);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';
$__html = '<div class="fg-blade-field fg-blade-field--accent-success">'
    . '<div class="fg-blade-template-meta"><span class="fg-blade-badge fg-blade-badge--success">Кастомный шаблон</span>'
    . '<span class="fg-blade-template-path">' . e($templateFile) . '</span></div>'
    . $element->render() . $error
    . '</div>';
@endphp
{!! $__html !!}
