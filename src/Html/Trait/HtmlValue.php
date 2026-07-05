<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;

trait HtmlValue
{
    protected function getHtmlValue(): string
    {
        return $this->getValue() !== '' ? ' value="' . HtmlEscaper::escapeAttribute($this->getValue()) . '"' : '';
    }
}
