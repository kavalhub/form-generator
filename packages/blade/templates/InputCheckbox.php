@php
$element->addClass(['fg-blade-check-input']);
$__html = '<label class="fg-blade-switch" for="' . $element->getId() . '">'
    . $element->renderControl()
    . '<span class="fg-blade-switch-track" aria-hidden="true"></span>'
    . '<span class="fg-blade-switch-label">' . $element->getLabel() . '</span>'
    . '</label>';
@endphp
{!! $__html !!}
