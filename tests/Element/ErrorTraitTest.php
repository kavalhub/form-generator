<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\Trait\Error;
use PHPUnit\Framework\TestCase;

final class ErrorTraitTest extends TestCase
{
    private object $element;

    protected function setUp(): void
    {
        $this->element = new class {
            use Error;
        };
    }

    public function testAddErrorMarksElementAsErrored(): void
    {
        $this->element->addError(['Ошибка']);

        $this->assertTrue($this->element->isError());
        $this->assertSame(['Ошибка'], $this->element->getError());
    }

    public function testGetDisplayErrorsEscapesHtml(): void
    {
        $this->element->addError(['<script>']);

        $this->assertSame('&lt;script&gt;', $this->element->getDisplayErrors());
    }

    public function testMultipleErrorsAreMerged(): void
    {
        $this->element->addError(['Первая']);
        $this->element->addError(['Вторая']);

        $this->assertSame(['Первая', 'Вторая'], $this->element->getError());
        $this->assertSame('Первая<br>Вторая', $this->element->getDisplayErrors());
    }

    public function testClearErrorsResetsState(): void
    {
        $this->element->addError(['Ошибка']);
        $this->element->clearErrors();

        $this->assertFalse($this->element->isError());
        $this->assertSame([], $this->element->getError());
    }
}
