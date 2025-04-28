<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlEnctype
{
    protected array $enctype = [];

    public function getEnctype(): array
    {
        return $this->enctype;
    }

    public function addEnctype(array $enctype): self
    {
        $this->enctype = array_merge($this->enctype, $enctype);

        return $this;
    }

    protected function getHtmlEnctype(): string
    {
        return !empty($this->getEnctype()) ? ' enctype="' . implode(' | ', $this->getEnctype()) . '"' : '';
    }
}
