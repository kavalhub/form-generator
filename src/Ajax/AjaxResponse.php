<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

final class AjaxResponse
{
    /** @var list<AjaxReplaceItem> */
    private array $replaces = [];

    private ?string $message = null;

    public function addReplace(AjaxReplaceItem $item): self
    {
        $this->replaces[] = $item;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'REPLACE' => array_map(static fn (AjaxReplaceItem $item): array => $item->toArray(), $this->replaces),
        ];
        if ($this->message !== null) {
            $data['MESSAGE'] = $this->message;
        }

        return $data;
    }

    public function jsonEncode(int $flags = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->toArray(), $flags) ?: '{}';
    }
}
