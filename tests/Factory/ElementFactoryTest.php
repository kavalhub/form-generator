<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Factory;

use Kavalhub\FormGenerator\Factory\ElementFactory;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Html\InputText;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ElementFactoryTest extends TestCase
{
    public function testCreateBuildsElementWithMethods(): void
    {
        $element = ElementFactory::create([
            ElementFactory::ELEMENT => InputText::class,
            ElementFactory::NAME => 'username',
            ElementFactory::METHOD => [
                ['setPlaceholder' => 'Enter name'],
                ['setRequired' => true],
            ],
        ]);

        $this->assertInstanceOf(InputText::class, $element);
        $this->assertSame('username', $element->getName());
        $this->assertSame('Enter name', $element->getPlaceholder());
        $this->assertTrue($element->isRequired());
    }

    public function testCreateGroupWithElementBlock(): void
    {
        $group = ElementFactory::create([
            ElementFactory::ELEMENT => Group::class,
            ElementFactory::NAME => 'g',
            ElementFactory::METHOD => [
                [
                    ElementFactory::ADD_ELEMENT_BLOCK => [
                        ElementFactory::ELEMENT => InputCheckbox::class,
                        ElementFactory::BLOCK => [
                            [
                                ElementFactory::NAME => 'opt',
                                ElementFactory::METHOD => [
                                    ['setDefaultValue' => '1', 'setLabel' => 'One'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertCount(1, iterator_to_array($group->getAll()));
    }

    public function testCreateThrowsWhenClassMissing(): void
    {
        $this->expectException(RuntimeException::class);
        ElementFactory::create([
            ElementFactory::ELEMENT => 'NonExistentClass',
            ElementFactory::NAME => 'x',
        ]);
    }

    public function testCreateThrowsWhenNameEmpty(): void
    {
        $this->expectException(RuntimeException::class);
        ElementFactory::create([
            ElementFactory::ELEMENT => InputText::class,
            ElementFactory::NAME => '',
        ]);
    }

    public function testGetClassNameMapsElementTypes(): void
    {
        $this->assertSame(InputCheckbox::class, ElementFactory::getClassName('InputCheckbox'));
        $this->assertSame(InputRadio::class, ElementFactory::getClassName('InputRadio'));
        $this->assertSame(InputText::class, ElementFactory::getClassName('InputText'));
    }
}
