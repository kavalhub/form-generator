<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Element\Trait\HtmlNovalidate;

class Form extends CompositeElement
{
    use HtmlNovalidate;
    use HtmlName;

    public function __construct(string $name)
    {
        $this->setName($name);
        parent::__construct();
    }

    public function getHtml(): string
    {
        $html = '';
        foreach ($this->elementStorage as $element) {
            $html .= $element->getHtml();
        }
        return '<form' . $this->getHtmlTrait() . '>' . $html . '</form>';
    }
}
