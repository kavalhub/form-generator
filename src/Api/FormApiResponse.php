<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Api;

final class FormApiResponse
{
    /** @param array<string, FormFieldState> $fields */
    public function __construct(
        private readonly bool $valid,
        private readonly array $fields = [],
        private readonly array $data = [],
        private readonly ?string $message = null,
        private readonly mixed $result = null,
    ) {
    }

    public function setMessage(string $message): self
    {
        return new self($this->valid, $this->fields, $this->data, $message, $this->result);
    }

    public function withResult(mixed $result): self
    {
        return new self($this->valid, $this->fields, $this->data, $this->message, $result);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $response = [
            'valid' => $this->valid,
            'fields' => array_map(static fn (FormFieldState $field): array => $field->toArray(), $this->fields),
            'data' => $this->data,
        ];
        if ($this->message !== null) {
            $response['message'] = $this->message;
        }
        if ($this->result !== null) {
            $response['result'] = $this->result;
        }

        return $response;
    }

    public function jsonEncode(int $flags = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->toArray(), $flags) ?: '{}';
    }
}
