<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Trait\HtmlMaxlength;
use Kavalhub\FormGenerator\Html\Trait\HtmlMinlength;
use Kavalhub\FormGenerator\Html\Trait\HtmlPattern;
use Kavalhub\FormGenerator\Html\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputText extends HtmlElementWithValue
{
    use HtmlMaxlength;
    use HtmlMinlength;
    use HtmlPattern;
    use HtmlPlaceholder;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('text');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
