<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;
use Kavalhub\FormGenerator\Html\Trait\HtmlCols;
use Kavalhub\FormGenerator\Html\Trait\HtmlMaxlength;
use Kavalhub\FormGenerator\Html\Trait\HtmlMinlength;
use Kavalhub\FormGenerator\Html\Trait\HtmlPlaceholder;
use Kavalhub\FormGenerator\Html\Trait\HtmlRows;
use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;

class Textarea extends HtmlElementWithValue
{
    use HtmlCols;
    use HtmlMaxlength;
    use HtmlMinlength;
    use HtmlPlaceholder;
    use HtmlRows;

    public function render(string $innerHtml = ''): string
    {
        return '<textarea' . $this->getHtmlTrait(['HtmlValue']) . '>' . HtmlEscaper::escape($this->getValue()) . '</textarea>';
    }
}
