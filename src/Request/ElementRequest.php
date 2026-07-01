<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request;

class ElementRequest extends AbstractElementRequest
{
    public function __construct(private ?array $source = null)
    {
    }

    public function get(string $name): ?array
    {
        $source = $this->source ?? $_POST;
        if (!isset($source[$name])) {
            return null;
        }
        $request = $source[$name];
        if (!is_array($request)) {
            $request = [$request];
        }

        return $request;
    }
}
