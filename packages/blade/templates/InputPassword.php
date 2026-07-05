@php
$element->addClass(['fg-blade-input']);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';
$__html = '<div class="fg-blade-field">' . $element->render() . $error . '</div>';
@endphp
{!! $__html !!}
