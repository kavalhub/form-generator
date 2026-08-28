<?php
declare(strict_types=1);

/** @var \Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator $this */
/** @var \Kavalhub\FormGenerator\Html\Paginator $element */
$element = $this->element;

$ajaxClass = $element->getClass();
if ($ajaxClass !== '') {
    $element->addClass(explode(' ', $ajaxClass));
}

if ($element->getNumPages() <= 1) {
    return '<div' . $element->getHtmlTrait(['HtmlName']) . '></div>';
}

$html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center mb-0">';
foreach ($element->getAll() as $childElement) {
    $classes = $childElement->getClassList();
    if (in_array('fg-paginator-ellipsis', $classes, true)) {
        $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        continue;
    }
    if (in_array('fg-paginator-current', $classes, true)) {
        /** @var \Kavalhub\FormGenerator\Html\Label $childElement */
        $html .= '<li class="page-item active" aria-current="page"><span class="page-link">'
            . htmlspecialchars($childElement->getLabel(), ENT_QUOTES) . '</span></li>';
        continue;
    }
    $html .= '<li class="page-item">' . $this->decorateChild($childElement)->render() . '</li>';
}
$html .= '</ul></nav>';

return '<div' . $element->getHtmlTrait(['HtmlName']) . '>' . $html . '</div>';
