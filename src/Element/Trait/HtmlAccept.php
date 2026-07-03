<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Util\HtmlEscaper;

trait HtmlAccept
{
    protected string $accept = '';

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function setAccept(string $accept): self
    {
        $this->accept = $accept;

        return $this;
    }

    protected function getHtmlAccept(): string
    {
        return $this->accept !== ''
            ? ' accept="' . HtmlEscaper::escapeAttribute($this->accept) . '"'
            : '';
    }
}
