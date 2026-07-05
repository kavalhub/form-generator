<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Trait\Label;
use Kavalhub\FormGenerator\Html\Trait\HtmlHref;

class Link extends HtmlElementWithValue
{
    use HtmlHref;
    use Label;

    public function __construct(string $name, string $href = '#', ?string $label = null)
    {
        parent::__construct($name);
        $this->setHref($href);
        $this->setLabel($label ?: $href);
    }

    public function render(string $innerHtml = ''): string
    {
        return '<a' . $this->getHtmlTrait() . '>' . $this->getLabel() . '</a>';
    }
}
