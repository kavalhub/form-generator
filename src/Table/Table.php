<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Table;

use Kavalhub\FormGenerator\Element\CompositeElement;

class Table extends CompositeElement
{
    public function __construct()
    {
        parent::__construct('table');
    }
}
