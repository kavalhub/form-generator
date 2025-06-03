<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Table;

use Kavalhub\FormGenerator\Element\Element;

class Th extends Element
{
    public function __construct(private $value)
    {
        parent::__construct('th');
    }

    public function getHtml(string $value = ''): string
    {
        return parent::getHtml($this->value);
    }
}
