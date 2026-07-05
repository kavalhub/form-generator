@php
$errorHtml = !empty($element->getError())
    ? '<div class="fg-blade-error">' . $element->getDisplayErrors() . '</div>'
    : '';
$element->addClass(['fg-blade-check-input']);
$__html = '<div class="fg-blade-field">'
    . '<label class="fg-blade-label" for="' . $element->getId() . '">' . $element->getTitle() . '</label>'
    . '<label class="fg-blade-switch" for="' . $element->getId() . '">'
    . $element->renderControl()
    . '<span class="fg-blade-switch-track" aria-hidden="true"></span>'
    . '</label>'
    . $errorHtml . '</div>';
@endphp
{!! $__html !!}
