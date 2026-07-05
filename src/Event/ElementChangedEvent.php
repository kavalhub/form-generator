<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Event;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

final readonly class ElementChangedEvent
{
    public function __construct(
        private ElementInterface $element,
    ) {
    }

    public function getElement(): ElementInterface
    {
        return $this->element;
    }
}
