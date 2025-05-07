<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator\Interface;
interface DecoratorInterface
{
    public function setTemplate(string $path): self;
    public function getErrorClass(): string;

    public function getSuccessClass(): string;

}
