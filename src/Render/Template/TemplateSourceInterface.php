<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Render\Template;

use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;

interface TemplateSourceInterface
{
    /**
     * @return list<string>
     */
    public function candidates(HtmlDecoratableInterface $element, TemplateResolutionContext $context): array;
}
