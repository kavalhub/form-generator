<?php

declare(strict_types=1);

namespace Kavalhub\FormGenerator\Storage;

/**
 * Реализация хранилища на основе PHP-сессий.
 */
class SessionElementStorage extends AbstractElementStorage
{
    private string $prefix;

    public function __construct(string $prefix = '_fg_')
    {
        $this->prefix = $prefix;
        $this->ensureSessionStarted();
    }

    public function get(string $key): mixed
    {
        $storageKey = $this->prefix . $key;
        return $_SESSION[$storageKey] ?? null;
    }

    protected function attach(string $key, mixed $value): void
    {
        $this->ensureSessionStarted();
        $storageKey = $this->prefix . $key;
        $_SESSION[$storageKey] = $value;
    }

    public function has(string $key): bool
    {
        $storageKey = $this->prefix . $key;
        return isset($_SESSION[$storageKey]);
    }

    public function remove(string $key): void
    {
        $storageKey = $this->prefix . $key;
        unset($_SESSION[$storageKey]);
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
