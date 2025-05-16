<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Form;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Trait\HtmlMaxlength;
use Kavalhub\FormGenerator\Element\Trait\HtmlPlaceholder;

class Textarea extends ElementWithValue
{
    use HtmlMaxlength;
    use HtmlPlaceholder;

    public function getHtml(string $value = ''): string
    {
        return '<textarea' . $this->getHtmlTrait(['HtmlValue']) . '>' . $this->getValue() . '</textarea>';
    }
}
