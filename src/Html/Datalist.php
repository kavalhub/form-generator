<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;

class Datalist extends HtmlCompositeElement
{
    private const POSTFIX = '_list';

    public function __construct(ElementWithValue $element, array $item = [])
    {
        parent::__construct();
        $this->setId($element->getId() . self::POSTFIX);
        $element->setList($this->getId());
        $this->setItem($item);
    }

    public function setItem(array $item): self
    {
        foreach ($item as $value) {
            $this->addElement(new Option((string)$value));
        }

        return $this;
    }

    public function render(string $innerHtml = ''): string
    {
        foreach ($this->elementStorage as $element) {
            if ($element instanceof HtmlRenderableInterface) {
                $innerHtml .= $element->render();
            }
        }

        return '<datalist' . $this->getHtmlTrait() . '>' . $innerHtml . '</datalist>';
    }
}
