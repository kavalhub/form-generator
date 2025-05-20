<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;

class Group extends CompositeElement
{
    use HtmlName;

    public function __construct(string $name, string $tag = 'div')
    {
        $this->setName($name);
        parent::__construct($tag);
    }

    public function getHtml(string $value = ''): string
    {
        foreach ($this->elementStorage as $element) {
            $value .= $element->getHtml();
        }

        return '<' . $this->tag . $this->getHtmlTrait(['HtmlName']) . '>' . $value . '</' . $this->tag . '>';
    }
}
