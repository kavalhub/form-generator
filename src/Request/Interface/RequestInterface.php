<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request\Interface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

interface RequestInterface
{
    public function get(string $name): ?array;

    public function setValue(ElementInterface $element): void;
}
