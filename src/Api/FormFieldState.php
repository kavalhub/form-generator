<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Api;

final class FormFieldState
{
    /**
     * @param list<string> $errors
     */
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly bool $valid,
        private readonly array $errors = [],
        private readonly mixed $value = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'valid' => $this->valid,
            'errors' => $this->errors,
        ];
        if ($this->value !== null) {
            $data['value'] = $this->value;
        }

        return $data;
    }
}
