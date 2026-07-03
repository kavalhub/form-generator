<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Util\HtmlEscaper;

trait HtmlAutocomplete
{
    protected string $autocomplete = '';

    public function getAutocomplete(): string
    {
        return $this->autocomplete;
    }

    public function setAutocomplete(string $autocomplete): self
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    protected function getHtmlAutocomplete(): string
    {
        return $this->autocomplete !== ''
            ? ' autocomplete="' . HtmlEscaper::escapeAttribute($this->autocomplete) . '"'
            : '';
    }
}
