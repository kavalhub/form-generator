<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Table;

use Kavalhub\FormGenerator\Element\Element;

class Td extends Element
{
    public function __construct(private $value)
    {
        parent::__construct('td');
    }

    public function getHtml(string $value = ''): string
    {
        return parent::getHtml($this->value);
    }
}
