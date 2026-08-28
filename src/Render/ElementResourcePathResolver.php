<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

final class ElementResourcePathResolver
{
    public function applyResourceBase(HtmlDecoratableInterface $element, ?string $resourceBase): void
    {
        $path = $element->getPath();
        if ($path === '' || $resourceBase === null) {
            return;
        }
        if (str_starts_with($path, '/') || is_file($path)) {
            return;
        }

        $element->setPath(rtrim($resourceBase, '/') . '/' . ltrim($path, '/'));
    }
}
