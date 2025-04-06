<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request\Interface;
interface RequestInterface
{
    public function get(string $name): ?array;
}
