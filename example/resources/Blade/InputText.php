@php
$templateFile = 'resources/Blade/InputText.php';
$element->addClass(['fg-blade-input']);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';
$__html = '<div class="fg-blade-field fg-blade-field--accent-primary">'
    . '<div class="fg-blade-template-meta"><span class="fg-blade-badge fg-blade-badge--primary">Кастомный шаблон</span>'
    . '<span class="fg-blade-template-path">' . e($templateFile) . '</span></div>'
    . $element->render() . $error
    . '</div>';
@endphp
{!! $__html !!}
