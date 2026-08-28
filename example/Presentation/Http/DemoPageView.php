<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Http;

final readonly class DemoPageView
{
    /**
     * @param array<string, string> $navItems
     */
    public function __construct(
        public string $page,
        public DecoratorMode $decoratorMode,
        public string $transport,
        public string $message,
        public string $headerHtml,
        public string $toolbarHtml,
        public string $navHtml,
        public string $flashHtml,
        public string $settingsHtml,
        public string $mainHtml,
        public string $bodyClass,
        public array $navItems,
    ) {
    }
}
