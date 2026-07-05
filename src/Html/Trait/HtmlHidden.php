<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

trait HtmlHidden
{
    protected bool $hidden = false;

    public function setHidden(bool $hidden = true): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    protected function getHtmlHidden(): string
    {
        return $this->hidden ? ' hidden' : '';
    }
}
