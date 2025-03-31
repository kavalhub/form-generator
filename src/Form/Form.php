<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Element\Trait\HtmlNovalidate;

class Form extends CompositeElement
{
    use HtmlNovalidate;
    use HtmlName;

    public function __construct(string $name)
    {
        parent::__construct();
        $this->setName($name);
    }

    public function addElement(ElementInterface $element, mixed $info = null): self
    {
        if (method_exists($element, 'setName')) {
            $element->setName($this->getName() . '_' . $element->getName());
        }
        $this->elementStorage->attach($element, $info ?? $element->getId());

        return $this;
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
