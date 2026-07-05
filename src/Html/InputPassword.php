<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Trait\HtmlMinlength;
use Kavalhub\FormGenerator\Html\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputPassword extends HtmlElementWithValue
{
    use HtmlMinlength;
    use HtmlPlaceholder;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('password');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
