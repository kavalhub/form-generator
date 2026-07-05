<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Tailwind;

use Kavalhub\FormGenerator\Decorator\AbstractDecorator;

class TailwindDecorator extends AbstractDecorator
{
    protected string $path = __DIR__ . '/../templates';
    protected string $errorClass = 'border-red-500 bg-red-50';
    protected string $successClass = 'border-emerald-500';

    public function getHtml(): string
    {
        $this->element->addClass(['mb-3']);

        return parent::getHtml();
    }
}
