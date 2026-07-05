<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;

trait HtmlName
{
    protected function getHtmlName(): string
    {
        $suffix = $this->isNameAsArray() ? '[]' : '';

        return $this->getName() !== ''
            ? ' name="' . HtmlEscaper::escapeAttribute($this->getFormName()) . $suffix . '"'
            : '';
    }
}
