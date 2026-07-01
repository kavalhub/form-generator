<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Fabric;

use Kavalhub\FormGenerator\Fabric\ElementFabric;
use Kavalhub\FormGenerator\Form\Group;
use Kavalhub\FormGenerator\Form\InputCheckbox;
use Kavalhub\FormGenerator\Form\InputRadio;
use Kavalhub\FormGenerator\Form\InputText;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ElementFabricTest extends TestCase
{
    public function testCreateBuildsElementWithMethods(): void
    {
        $element = ElementFabric::create([
            ElementFabric::ELEMENT => InputText::class,
            ElementFabric::NAME => 'username',
            ElementFabric::METHOD => [
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
        $group = ElementFabric::create([
            ElementFabric::ELEMENT => Group::class,
            ElementFabric::NAME => 'g',
            ElementFabric::METHOD => [
                [
                    ElementFabric::ADD_ELEMENT_BLOCK => [
                        ElementFabric::ELEMENT => InputCheckbox::class,
                        ElementFabric::BLOCK => [
                            [
                                ElementFabric::NAME => 'opt',
                                ElementFabric::METHOD => [
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
        ElementFabric::create([
            ElementFabric::ELEMENT => 'NonExistentClass',
            ElementFabric::NAME => 'x',
        ]);
    }

    public function testCreateThrowsWhenNameEmpty(): void
    {
        $this->expectException(RuntimeException::class);
        ElementFabric::create([
            ElementFabric::ELEMENT => InputText::class,
            ElementFabric::NAME => '',
        ]);
    }

    public function testGetClassNameMapsElementTypes(): void
    {
        $this->assertSame(InputCheckbox::class, ElementFabric::getClassName('InputCheckbox'));
        $this->assertSame(InputRadio::class, ElementFabric::getClassName('InputRadio'));
    }
}
