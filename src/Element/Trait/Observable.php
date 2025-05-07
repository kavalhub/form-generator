<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Element\Trait;

use Kavalhub\FormGenerator\Observer\ElementObserverInterface;
use SplObjectStorage;

trait Observable
{
    protected SplObjectStorage $observer;

    public function attachObserver(ElementObserverInterface $elementObserver): self
    {
        $this->observer->attach($elementObserver);

        return $this;
    }

    public function detachObserver(ElementObserverInterface $elementObserver): self
    {
        $this->observer->detach($elementObserver);

        return $this;
    }

    public function notify(): self
    {
        foreach ($this->observer as $observer) {
            $observer->update($this);
        }
        return $this;
    }
}
