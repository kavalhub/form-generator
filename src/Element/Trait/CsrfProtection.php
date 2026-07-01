<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Form\InputHidden;
use Kavalhub\FormGenerator\Request\Interface\RequestInterface;

trait CsrfProtection
{
    private bool $csrfEnabled = false;
    private string $csrfFieldName = 'csrf';
    private ?InputHidden $csrfField = null;

    public function enableCsrf(): self
    {
        if ($this->csrfEnabled) {
            return $this;
        }
        $this->csrfEnabled = true;
        $this->csrfField = (new InputHidden($this->csrfFieldName))
            ->setValue($this->getOrCreateCsrfToken());
        $this->addElement($this->csrfField);

        return $this;
    }

    public function isCsrfEnabled(): bool
    {
        return $this->csrfEnabled;
    }

    public function validateCsrfToken(RequestInterface $request): bool
    {
        if (!$this->csrfEnabled || $this->csrfField === null) {
            return true;
        }
        $submitted = $request->get($this->csrfField->getFormName());

        return isset($submitted[0]) && hash_equals($this->getOrCreateCsrfToken(), (string)$submitted[0]);
    }

    private function getOrCreateCsrfToken(): string
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
