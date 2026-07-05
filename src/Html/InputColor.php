<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputColor extends HtmlElementWithValue
{
    use HtmlPlaceholder;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('color');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
