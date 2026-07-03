<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Util\HtmlEscaper;

trait HtmlList
{
    protected string $list = '';

    public function getList(): string
    {
        return !empty($this->parent) ? $this->parent->getId() . '_' . $this->list : $this->list;
    }

    public function setList(string $list): self
    {
        $this->list = $list;

        return $this;
    }

    protected function getHtmlList(): string
    {
        return !empty($this->list)
            ? ' list="' . HtmlEscaper::escapeAttribute($this->getList()) . '"'
            : '';
    }
}
