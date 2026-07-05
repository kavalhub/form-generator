<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Interface;

use Kavalhub\FormGenerator\Request\Interface\RequestInterface;

interface CsrfProtectable
{
    public function isCsrfEnabled(): bool;

    public function validateCsrfToken(RequestInterface $request): bool;
}
