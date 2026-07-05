<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Request\Interface\RequestInterface;

trait CsrfProtection
{
    private bool $csrfEnabled = false;
    protected string $csrfFieldName = 'csrf';

    public function enableCsrf(): self
    {
        $this->csrfEnabled = true;

        return $this;
    }

    public function isCsrfEnabled(): bool
    {
        return $this->csrfEnabled;
    }

    public function getCsrfFormName(): string
    {
        return $this->getId() . '_' . $this->csrfFieldName;
    }

    public function validateCsrfToken(RequestInterface $request): bool
    {
        if (!$this->csrfEnabled) {
            return true;
        }
        $submitted = $request->get($this->getCsrfFormName());

        return isset($submitted[0]) && hash_equals($this->getOrCreateCsrfToken(), (string)$submitted[0]);
    }

    protected function getOrCreateCsrfToken(): string
    {
        $sessionKey = '_form_csrf_' . $this->getFormName();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($_SESSION[$sessionKey])) {
            return (string)$_SESSION[$sessionKey];
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION[$sessionKey] = $token;

        return $token;
    }
}
