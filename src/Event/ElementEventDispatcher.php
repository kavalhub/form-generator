<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Event;

final class ElementEventDispatcher
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    public function addListener(string $eventClass, callable $listener): self
    {
        $this->listeners[$eventClass][] = $listener;

        return $this;
    }

    public function dispatch(object $event): void
    {
        $eventClass = $event::class;
        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            $listener($event);
        }
    }
}
