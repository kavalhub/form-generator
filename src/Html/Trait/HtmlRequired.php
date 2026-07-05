<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlRequired
{
    protected function getHtmlRequired(): string
    {
        return $this->isRequired() ? ' required' : '';
    }
}
