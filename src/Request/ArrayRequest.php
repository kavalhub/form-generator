<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request;

class ArrayRequest extends AbstractElementRequest
{
    public function __construct(private array $data = [])
    {
    }

    public function get(string $name): ?array
    {
        if (!isset($this->data[$name])) {
            return null;
        }
        $request = $this->data[$name];
        if (!is_array($request)) {
            $request = [$request];
        }

        return $request;
    }
}
