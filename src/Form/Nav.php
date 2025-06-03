<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElementWithValue;

class Nav extends CompositeElementWithValue
{
    public function __construct(string $name, string $tag = 'Nav')
    {
        parent::__construct($name, $tag);
    }

    public function getHtml(string $value = ''): string
    {
        foreach ($this->elementStorage as $element) {
            $value .= $element->getHtml();
        }

        return '<' . $this->tag . $this->getHtmlTrait() . '>' . $value . '</' . $this->tag . '>';
    }
}
