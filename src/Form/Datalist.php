<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\ElementWithValue;

class Datalist extends CompositeElement
{

    private const POSTFIX = '_list';

    public function __construct(ElementWithValue $element, array $item = [])
    {
        parent::__construct();
        $this->setId(id: $element->getId() . self::POSTFIX);
        $element->setList(list: $this->getId());
        $this->setItem(item: $item);
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
