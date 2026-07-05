<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlClass
{
    protected function getHtmlClass(): string
    {
        return !empty($this->getClassList()) ? ' class="' . implode(' ', $this->getClassList()) . '"' : '';
    }
}
