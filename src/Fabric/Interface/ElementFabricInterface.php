<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Fabric\Interface;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

interface ElementFabricInterface
{
    public static function create(array $elementData): ElementInterface;
}
