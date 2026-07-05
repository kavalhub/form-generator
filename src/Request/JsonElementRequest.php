<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Request;

use InvalidArgumentException;

class JsonElementRequest extends AbstractElementRequest
{
    public function __construct(private array $data = [])
    {
    }

    public static function fromJson(string $json): self
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Invalid JSON body');
        }

        return self::fromArray($decoded);
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
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

    public function merge(array $data): self
    {
        return new self(array_merge($this->data, $data));
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }
}
