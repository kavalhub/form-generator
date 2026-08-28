@php
/** @var \Kavalhub\FormGenerator\Blade\BladeDecorator $decorator */
/** @var \Kavalhub\FormGenerator\Html\Paginator $element */
$ajaxClass = $element->getClass();
if ($ajaxClass !== '') {
    $element->addClass(explode(' ', $ajaxClass));
}

if ($element->getNumPages() <= 1) {
    $__html = '<div' . $element->getHtmlTrait(['HtmlName']) . '></div>';
} else {
    $html = '<nav aria-label="Pagination"><ul class="fg-blade-pagination">';
    foreach ($element->getAll() as $childElement) {
        $classes = $childElement->getClassList();
        if (in_array('fg-paginator-ellipsis', $classes, true)) {
            $html .= '<li class="fg-blade-pagination-ellipsis"><span>...</span></li>';
            continue;
        }
        if (in_array('fg-paginator-current', $classes, true)) {
            /** @var \Kavalhub\FormGenerator\Html\Label $childElement */
            $html .= '<li class="fg-blade-pagination-current"><span>' . e($childElement->getLabel()) . '</span></li>';
            continue;
        }
        $html .= '<li>' . $decorator->decorateChild($childElement)->render() . '</li>';
    }
    $html .= '</ul></nav>';

    $__html = '<div' . $element->getHtmlTrait(['HtmlName']) . '>' . $html . '</div>';
}
@endphp
{!! $__html !!}
