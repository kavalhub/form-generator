<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlId
{
    protected function getHtmlId(): string
    {
        return $this->getThisId() !== '' ? ' id="' . $this->getId() . '"' : '';
    }
}
