<?php
declare(strict_types=1);

namespace SimpleExample\Event;

readonly class RendererChangedEvent
{
    public function __construct(
        public string $theme,
        public string $transport,
    ) {
    }
}
