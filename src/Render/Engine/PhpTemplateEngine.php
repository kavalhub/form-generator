<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Engine;

final class PhpTemplateEngine implements TemplateEngineInterface
{
    public function render(string $path, object $context): string
    {
        $vars = $this->publicProperties($context);

        $render = function (string $templatePath) use ($vars): string {
            extract($vars, EXTR_SKIP);

            ob_start();
            $result = include $templatePath;
            $buffer = (string) ob_get_clean();

            if (is_string($result) && $result !== '' && $result !== '1') {
                return $result;
            }

            return $buffer;
        };

        $bound = $render->bindTo($context, $context);

        return $bound($path);
    }

    /**
     * @return array<string, mixed>
     */
    private function publicProperties(object $context): array
    {
        $vars = [];
        foreach ((new \ReflectionObject($context))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $vars[$property->getName()] = $property->getValue($context);
        }

        return $vars;
    }
}
