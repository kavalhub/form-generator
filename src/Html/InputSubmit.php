<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Element\Interface\SkipsValueCollection;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputSubmit extends HtmlElementWithValue implements SkipsValueCollection
{
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('submit');
    }

    public function setValue(string $value): self
    {
        return $this;
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
