<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\Trait\Error;
use Kavalhub\FormGenerator\Element\Trait\HtmlMultiple;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;
use Kavalhub\FormGenerator\Element\Trait\Valid;

class Select extends CompositeElement
{
    use Error;
    use HtmlName;
    use HtmlValue;
    use HtmlMultiple;
    use Valid;

    public function __construct(string $name, array $item = [])
    {
        parent::__construct();
        $this->setName($name);
        $this->setItem($item);
    }

    public function setItem(array $item): self
    {
        foreach ($item as $key => $value) {
            $this->addItem((string)$key, (string)$value);
        }

        return $this;
    }

    public function addItem(string $key, string $value): self
    {
        return $this->addElement(new Option($key, $value));
    }

    public function getSelected(): array
    {
        $array = [];
        foreach ($this->elementStorage as $item) {
            if ($item->isSelected()) {
                $array[] = $item->getValue();
            }
        }

        return $array;
    }

    public function setValue($value): self
    {
        foreach ($this->getAll() as $element) {
            $element->setValue((string)$value);
        }

        return $this;
    }

    public function getHtml(): string
    {
        $html = '';
        foreach ($this->elementStorage as $element) {
            $html .= $element->getHtml();
        }

        return '<select' . $this->getHtmlTrait() . '>' . $html . '</select>';
    }
}
