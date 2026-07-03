<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Factory\Interface;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

interface ElementFactoryInterface
{
    public static function create(array $elementData): ElementInterface;
}
