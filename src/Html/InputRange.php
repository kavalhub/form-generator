<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Trait\HtmlMax;
use Kavalhub\FormGenerator\Html\Trait\HtmlMin;
use Kavalhub\FormGenerator\Html\Trait\HtmlStep;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputRange extends HtmlElementWithValue
{
    use HtmlMax;
    use HtmlMin;
    use HtmlStep;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('range');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
