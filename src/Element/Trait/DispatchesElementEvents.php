<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Event\ElementChangedEvent;
use Kavalhub\FormGenerator\Event\ElementEventDispatcher;

trait DispatchesElementEvents
{
    protected ?ElementEventDispatcher $dispatcher = null;

    public function setDispatcher(ElementEventDispatcher $dispatcher): self
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function getDispatcher(): ?ElementEventDispatcher
    {
        return $this->dispatcher;
    }

    public function notify(): self
    {
        if ($this->dispatcher !== null) {
            $this->dispatcher->dispatch(new ElementChangedEvent($this));
        }

        return $this;
    }
}
