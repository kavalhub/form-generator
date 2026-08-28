<?php
declare(strict_types=1);

/** @var \Kavalhub\FormGenerator\Tailwind\TailwindDecorator $this */
/** @var \Kavalhub\FormGenerator\Html\Paginator $element */
$element = $this->element;

$ajaxClass = $element->getClass();
if ($ajaxClass !== '') {
    $element->addClass(explode(' ', $ajaxClass));
}

if ($element->getNumPages() <= 1) {
    return '<div' . $element->getHtmlTrait(['HtmlName']) . '></div>';
}

$html = '<nav aria-label="Pagination"><ul class="inline-flex items-center gap-1">';
foreach ($element->getAll() as $childElement) {
    $classes = $childElement->getClassList();
    if (in_array('fg-paginator-ellipsis', $classes, true)) {
        $html .= '<li><span class="px-3 py-1 text-gray-400">...</span></li>';
        continue;
    }
    if (in_array('fg-paginator-current', $classes, true)) {
        /** @var \Kavalhub\FormGenerator\Html\Label $childElement */
        $html .= '<li><span class="px-3 py-1 rounded bg-blue-600 text-white">'
            . htmlspecialchars($childElement->getLabel(), ENT_QUOTES) . '</span></li>';
        continue;
    }
    $html .= '<li>' . $this->decorateChild($childElement)->render() . '</li>';
}
$html .= '</ul></nav>';

return '<div' . $element->getHtmlTrait(['HtmlName']) . '>' . $html . '</div>';
