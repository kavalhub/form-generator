<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Api\Interface;

interface FormApiDescribable
{
    public function getApiKey(): string;

    /**
     * @return list<string>
     */
    public function getApiActions(): array;
}
