<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Trait\Valid;
use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlDisabled;
use Kavalhub\FormGenerator\Html\Trait\HtmlMultiple;
use Kavalhub\FormGenerator\Html\Trait\HtmlSize;

class Select extends HtmlCompositeElementWithValue
{
    use HtmlDisabled;
    use HtmlMultiple;
    use HtmlSize;
    use Valid;

    public function __construct(string $name, array $item = [])
    {
        parent::__construct($name);
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

    public function setValue(string $value): self
    {
        $this->value = $value;
        foreach ($this->getAll() as $element) {
            $element->setValue($value);
        }

        return $this;
    }

    public function getValue(): string
    {
        $selected = $this->getSelected();

        return $selected[0] ?? ($this->value !== '' ? $this->value : $this->defaultValue);
    }

    public function render(string $innerHtml = ''): string
    {
        foreach ($this->elementStorage as $element) {
            if ($element instanceof HtmlRenderableInterface) {
                $innerHtml .= $element->render();
            }
        }

        return '<select' . $this->getHtmlTrait() . '>' . $innerHtml . '</select>';
    }
}
