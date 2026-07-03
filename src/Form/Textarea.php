<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlCols;
use Kavalhub\FormGenerator\Element\Trait\HtmlMaxlength;
use Kavalhub\FormGenerator\Element\Trait\HtmlMinlength;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Element\Trait\HtmlRows;
use Kavalhub\FormGenerator\Util\HtmlEscaper;

class Textarea extends ElementWithValue
{
    use HtmlCols;
    use HtmlMaxlength;
    use HtmlMinlength;
    use HtmlPlaceholder;
    use HtmlRows;

    public function getHtml(string $value = ''): string
    {
        return '<textarea' . $this->getHtmlTrait(['HtmlValue']) . '>' . HtmlEscaper::escape($this->getValue()) . '</textarea>';
    }
}
