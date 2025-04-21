<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\Trait\Error;
use Kavalhub\FormGenerator\Element\Trait\HtmlName;
use Kavalhub\FormGenerator\Element\Trait\HtmlValue;
use Kavalhub\FormGenerator\Element\Trait\Valid;

class Select extends CompositeElement
{
    use Error;
    use HtmlName;
    use HtmlValue;
    use Valid;

    public function __construct(string $name, array $item)
    {
        parent::__construct();
        $this->setName($name);
        foreach ($item as $key => $value) {
            $this->addElement(new Option((string)$key, (string)$value));
        }
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
