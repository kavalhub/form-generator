<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Laravel;

use Illuminate\Http\Request;
use Kavalhub\FormGenerator\Request\AbstractElementRequest;

final class LaravelRequestAdapter extends AbstractElementRequest
{
    public function __construct(private readonly Request $request)
    {
    }

    public function get(string $name): ?array
    {
        if (!$this->request->has($name)) {
            return null;
        }
        $value = $this->request->input($name);
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            $value = [$value];
        }

        return $value;
    }
}
