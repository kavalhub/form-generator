<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request;

class PostOnlyRequest extends AbstractElementRequest
{
    public function get(string $name): ?array
    {
        if (!isset($_POST[$name])) {
            return null;
        }
        $request = $_POST[$name];
        if (!is_array($request)) {
            $request = [$request];
        }

        return $request;
    }
}
