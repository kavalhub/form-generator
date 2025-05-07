<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;

class Group extends CompositeElement
{
    use HtmlName;

    public function __construct(string $name)
    {
        $this->setName($name);
        parent::__construct();
    }

    public function getHtml(): string
    {
        foreach ($this->elementStorage as $element) {
            $html .= $element->getHtml();
        }

        return '<div' . $this->getHtmlTrait(['HtmlName']) . '>' . $html . '</div>';
    }
}
