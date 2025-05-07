<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

trait CallbackValidator
{
    protected array $callbackValidatorList = [];

    public function addCallbackValidator(callable $validator): self
    {
        if (is_callable($validator)) {
            $this->callbackValidatorList[] = $validator;
        }

        return $this;
    }

    public function getCallbackValidatorList(): array
    {
        return $this->callbackValidatorList;
    }
}
