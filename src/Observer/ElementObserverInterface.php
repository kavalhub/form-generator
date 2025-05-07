<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Observer;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;

interface ElementObserverInterface
{
    public function update(ElementInterface $element);
}