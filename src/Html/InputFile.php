<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Trait\HtmlAccept;
use Kavalhub\FormGenerator\Html\Trait\HtmlMultiple;
use Kavalhub\FormGenerator\Html\Trait\HtmlType;

class InputFile extends HtmlElementWithValue
{
    use HtmlAccept;
    use HtmlMultiple;
    use HtmlType;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->setType('file');
    }

    public function render(string $innerHtml = ''): string
    {
        return '<input' . $this->getHtmlTrait() . '>';
    }
}
