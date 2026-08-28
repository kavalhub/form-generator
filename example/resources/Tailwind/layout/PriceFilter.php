<?php
declare(strict_types=1);

/** @var \Kavalhub\FormGenerator\Tailwind\TailwindDecorator $this */
$children = iterator_to_array($this->element->getAll(), false);
$minEl = $children[0] ?? null;
$maxEl = $children[1] ?? null;
$this->element->addClass(['demo-price-filter']);
$body = '<div class="demo-price-filter__rows space-y-3">';
if ($minEl !== null) {
    $body .= '<div class="demo-price-filter__row flex flex-wrap items-center gap-2">'
        . '<span class="demo-price-filter__caption text-sm font-medium text-slate-600">От</span>'
        . '<span class="js-price-min-label demo-price-label text-sm tabular-nums text-slate-800"></span>'
        . $this->decorateChild($minEl)->render()
        . '</div>';
}
if ($maxEl !== null) {
    $body .= '<div class="demo-price-filter__row flex flex-wrap items-center gap-2">'
        . '<span class="demo-price-filter__caption text-sm font-medium text-slate-600">До</span>'
        . '<span class="js-price-max-label demo-price-label text-sm tabular-nums text-slate-800"></span>'
        . $this->decorateChild($maxEl)->render()
        . '</div>';
}
$body .= '</div>';

return '<div' . $this->element->getHtmlTrait(['HtmlName']) . '>' . $body . '</div>';
