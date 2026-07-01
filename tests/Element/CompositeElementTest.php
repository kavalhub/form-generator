<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Element;

use Kavalhub\FormGenerator\Element\CompositeElement;
use Kavalhub\FormGenerator\Element\NullElement;
use Kavalhub\FormGenerator\Form\Group;
use Kavalhub\FormGenerator\Form\InputCheckbox;
use Kavalhub\FormGenerator\Form\InputNumber;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Form\Label;
use PHPUnit\Framework\TestCase;

final class CompositeElementTest extends TestCase
{
    public function testAddElementSetsParent(): void
    {
        $group = new Group('g');
        $input = new InputText('name');
        $group->addElement($input);

        $this->assertSame($group, $input->getParent());
        $this->assertSame('g_name', $input->getFormName());
    }

    public function testGetByNameFindsNestedElement(): void
    {
        $group = new Group('g');
        $input = new InputText('email');
        $group->addElement($input);

        $form = new CompositeElement();
        $form->addElement($group);

        $found = $form->getByName('email');
        $this->assertInstanceOf(InputText::class, $found);
        $this->assertSame('email', $found->getName());
    }

    public function testGetByNameReturnsNullElementWhenMissing(): void
    {
        $form = new CompositeElement();
        $found = $form->getByName('missing');

        $this->assertInstanceOf(NullElement::class, $found);
    }

    public function testGetByTypeCollectsNestedElements(): void
    {
        $form = new CompositeElement();
        $group = new Group('g');
        $label = new Label('title');
        $input = new InputText('name');

        $group->addElement($input);
        $form->addElement($group);
        $form->addElement($label);

        $labels = $form->getByType(Label::class);
        $inputs = $form->getByType(InputText::class);

        $this->assertCount(1, iterator_to_array($labels->getAll()));
        $this->assertCount(1, iterator_to_array($inputs->getAll()));
    }

    public function testGetValueArrayCollectsTextAndNumberFields(): void
    {
        $form = new CompositeElement();
        $text = (new InputText('name'))->setValue('Alice');
        $number = (new InputNumber('age'))->setValue('30');

        $form->addElement($text);
        $form->addElement($number);

        $this->assertSame(
            ['name' => 'Alice', 'age' => '30'],
            $form->getValueArray()
        );
    }

    public function testGetValueArrayCollectsCheckedCheckboxes(): void
    {
        $form = new CompositeElement();
        $checked = (new InputCheckbox('colors', 'red'))->setChecked();
        $unchecked = new InputCheckbox('colors', 'blue');

        $form->addElement($checked);
        $form->addElement($unchecked);

        $this->assertSame(['colors' => ['red']], $form->getValueArray());
    }

    public function testGetValueArraySkipsSubmitButton(): void
    {
        $form = new CompositeElement();
        $form->addElement((new InputText('name'))->setValue('Bob'));
        $form->addElement((new InputSubmit('send'))->setDefaultValue('Go'));

        $values = $form->getValueArray();
        $this->assertArrayNotHasKey('send', $values);
        $this->assertSame(['name' => 'Bob'], $values);
    }

    public function testGetValueArrayMergesNestedGroupValues(): void
    {
        $form = new CompositeElement();
        $group = new Group('g');
        $group->addElement((new InputText('city'))->setValue('Moscow'));
        $form->addElement($group);

        $this->assertSame(['city' => 'Moscow'], $form->getValueArray());
    }
}
