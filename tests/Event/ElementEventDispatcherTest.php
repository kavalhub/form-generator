<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Event;

use Kavalhub\FormGenerator\Event\ElementChangedEvent;
use Kavalhub\FormGenerator\Event\ElementEventDispatcher;
use Kavalhub\FormGenerator\Html\InputText;
use PHPUnit\Framework\TestCase;

final class ElementEventDispatcherTest extends TestCase
{
    public function testDispatchCallsRegisteredListener(): void
    {
        $dispatcher = new ElementEventDispatcher();
        $calls = [];
        $dispatcher->addListener(ElementChangedEvent::class, static function (ElementChangedEvent $event) use (&$calls): void {
            $calls[] = $event->getElement()->getName();
        });

        $input = new InputText('email');
        $dispatcher->dispatch(new ElementChangedEvent($input));

        $this->assertSame(['email'], $calls);
    }

    public function testNotifyDispatchesWhenDispatcherIsSet(): void
    {
        $dispatcher = new ElementEventDispatcher();
        $calls = 0;
        $dispatcher->addListener(ElementChangedEvent::class, static function () use (&$calls): void {
            $calls++;
        });

        $input = (new InputText('name'))->setDispatcher($dispatcher);
        $input->notify();

        $this->assertSame(1, $calls);
    }
}
