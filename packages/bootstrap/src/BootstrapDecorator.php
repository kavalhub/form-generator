<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Bootstrap;

use Kavalhub\FormGenerator\Decorator\AbstractDecorator;

class BootstrapDecorator extends AbstractDecorator
{
    protected string $path = __DIR__ . '/../templates';
    protected string $errorClass = 'is-invalid';
    protected string $successClass = 'is-valid';

    public function getHtml(): string
    {
        $this->element->addClass(['mb-2']);

        return parent::getHtml();
    }
}
