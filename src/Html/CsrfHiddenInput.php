<?php

declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html;

class CsrfHiddenInput extends InputHidden
{
    public function __construct(string $name = 'csrf_token')
    {
        parent::__construct($name);
        // CSRF ВСЕГДА использует хранилище
        $this->useStorage();
    }

    /**
     * Специальный метод только для валидатора, чтобы установить токен для рендеринга
     */
    public function setStoredValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Игнорируем попытки установить значение из HTTP-запроса (через Request::setValue)
     */
    public function setValue(string $value): self
    {
        // Ничего не делаем, значение управляется только через setStoredValue()
        return $this;
    }

    public function validateToken(string $submittedToken, string $storedToken): bool
    {
        if ($submittedToken === '') {
            return false;
        }
        return hash_equals($storedToken, $submittedToken);
    }
}