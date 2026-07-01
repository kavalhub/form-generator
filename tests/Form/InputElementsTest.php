<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Form;

use Kavalhub\FormGenerator\Form\InputCheckbox;
use Kavalhub\FormGenerator\Form\InputNumber;
use Kavalhub\FormGenerator\Form\InputPassword;
use Kavalhub\FormGenerator\Form\InputRadio;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Form\Option;
use Kavalhub\FormGenerator\Form\Select;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use PHPUnit\Framework\TestCase;

final class InputElementsTest extends TestCase
{
    public function testCheckboxChecksOnMatchingValue(): void
    {
        $checkbox = new InputCheckbox('agree', 'yes');
        $checkbox->setValue('yes');

        $this->assertTrue($checkbox->isChecked());
    }

    public function testCheckboxStaysUncheckedOnDifferentValue(): void
    {
        $checkbox = new InputCheckbox('agree', 'yes');
        $checkbox->setValue('no');

        $this->assertFalse($checkbox->isChecked());
    }

    public function testInputNumberRendersTypeNumber(): void
    {
        $input = new InputNumber('qty');
        $html = $input->getHtml();

        $this->assertStringContainsString('type="number"', $html);
    }

    public function testInputPasswordRendersTypePassword(): void
    {
        $input = new InputPassword('pass');
        $html = $input->getHtml();

        $this->assertStringContainsString('type="password"', $html);
    }

    public function testSelectSetValueSelectsOption(): void
    {
        $select = new Select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $select->setValue('blue');

        $this->assertSame(['blue'], $select->getSelected());
    }

    public function testOptionUsesParentFormName(): void
    {
        $select = new Select('country');
        $select->addItem('ru', 'Russia');
        /** @var Option $option */
        $option = iterator_to_array($select->getAll())[0];

        $this->assertSame('country', $option->getFormName());
    }

    public function testRequestBindsValueToInputText(): void
    {
        $input = new InputText('login');
        (new ArrayRequest(['login' => 'admin']))->setValue($input);

        $this->assertSame('admin', $input->getValue());
    }

    public function testPlaceholderIsEscaped(): void
    {
        $input = (new InputText('q'))->setPlaceholder('"><x>');
        $html = $input->getHtml();

        $this->assertStringContainsString('placeholder="&quot;&gt;&lt;x&gt;"', $html);
    }

    public function testInputRadioChecksOnMatchingValue(): void
    {
        $radio = new InputRadio('size', 'l');
        $radio->setValue('l');

        $this->assertTrue($radio->isChecked());
    }
}
