@php
$element->addClass(['fg-blade-textarea']);
$error =
    !empty($element->getError()) ? '<div class="fg-blade-error">' . $element->getDisplayErrors()
        . '</div>' : '';
$__html = '<div class="fg-blade-field">' . $element->render() . $error . '</div>';
@endphp
{!! $__html !!}
