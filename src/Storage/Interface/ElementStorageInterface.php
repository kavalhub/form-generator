<?php

declare(strict_types=1);

namespace Kavalhub\FormGenerator\Storage\Interface;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

/**
 * Интерфейс для хранения значений элементов
 */
interface ElementStorageInterface
{
    public function get(string $key): mixed;

    public function set(ElementInterface $element): void;

    public function has(string $key): bool;

    public function remove(string $key): void;

    public function setDefaultValue(ElementInterface $element): void;
}