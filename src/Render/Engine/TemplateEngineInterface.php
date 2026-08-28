<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Engine;

interface TemplateEngineInterface
{
    public function render(string $path, object $context): string;
}
