<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request;

class ElementRequest extends AbstractElementRequest
{
    public function get(string $name): ?array
    {
        if (!isset($_REQUEST[$name])) {
            return null;
        }
        $request = $_REQUEST[$name];
        if (!is_array($request)) {
            $request = [$request];
        }

        return $request;
    }
}