<?php
declare(strict_types=1);

/** @var \Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator $this */
$children = iterator_to_array($this->element->getAll(), false);
$minEl = $children[0] ?? null;
$maxEl = $children[1] ?? null;
$this->element->addClass(['demo-price-filter']);
$body = '<div class="demo-price-filter__rows">';
if ($minEl !== null) {
    $body .= '<div class="demo-price-filter__row">'
        . '<span class="demo-price-filter__caption">От</span>'
        . '<span class="js-price-min-label demo-price-label"></span>'
        . $this->decorateChild($minEl)->render()
        . '</div>';
}
if ($maxEl !== null) {
    $body .= '<div class="demo-price-filter__row">'
        . '<span class="demo-price-filter__caption">До</span>'
        . '<span class="js-price-max-label demo-price-label"></span>'
        . $this->decorateChild($maxEl)->render()
        . '</div>';
}
$body .= '</div>';

return '<' . $this->element->getTag() . $this->element->getHtmlTrait(['HtmlName']) . '>'
    . $body
    . '</' . $this->element->getTag() . '>';
