<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\ElementWithValue;

class Datalist extends CompositeElement
{

    public function __construct(ElementWithValue $element, array $item = [])
    {
        parent::__construct();
        $this->setId($element->getId() . '_list');
        $element->setList($this->getId() . '_list');
        $this->setItem($item);
    }

    public function setItem(array $item): self
    {
        foreach ($item as $value) {
            $this->addElement(new Option((string)$value));
        }

        return $this;
    }

    public function getHtml(): string
    {
        $html = '';
        foreach ($this->elementStorage as $element) {
            $html .= $element->getHtml();
        }

        return '<datalist' . $this->getHtmlTrait() . '>' . $html . '</datalist>';
    }
}
