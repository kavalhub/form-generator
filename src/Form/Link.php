<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlHref;
use Kavalhub\FormGenerator\Element\Trait\Label;

class Link extends ElementWithValue
{
    use HtmlHref;
    use Label;

    public function __construct(string $name, string $href = '#', string $label = null)
    {
        parent::__construct($name);
        $this->setHref($href);
        $this->setLabel($label ?: $href);
    }

    public function getHtml(string $value = ''): string
    {
        return '<a' . $this->getHtmlTrait() . '>' . $this->getLabel() . '</a>';
    }
}
