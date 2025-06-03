<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait HtmlData
{
    protected array $dataList = [];

    public function addData(array $data): self
    {
        $this->dataList = array_merge($this->dataList, $data);

        return $this;
    }

    public function removeData(array $class): self
    {
        $this->dataList = array_diff($this->dataList, $class);

        return $this;
    }

    protected function getHtmlData(): string
    {
        $html = [];
        foreach ($this->dataList as $key => $data) {
            $html[] = 'data-' . $key . '="' . $data . '"';
        }
        return implode(' ', $html);
    }
}
