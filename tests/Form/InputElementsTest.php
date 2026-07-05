<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Form;

use Kavalhub\FormGenerator\Html\InputCheckbox;
use Kavalhub\FormGenerator\Html\InputNumber;
use Kavalhub\FormGenerator\Html\InputPassword;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Option;
use Kavalhub\FormGenerator\Html\Select;
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
        $html = $input->render();

        $this->assertStringContainsString('type="number"', $html);
    }

    public function testInputPasswordRendersTypePassword(): void
    {
        $input = new InputPassword('pass');
        $html = $input->render();

        $this->assertStringContainsString('type="password"', $html);
    }

    public function testSelectSetValueSelectsOption(): void
    {
        $select = new Select('color', ['red' => 'Red', 'blue' => 'Blue']);
        $select->setValue('blue');

        $this->assertSame(['blue'], $select->getSelected());
        $this->assertSame('blue', $select->getValue());
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
        $html = $input->render();

        $this->assertStringContainsString('placeholder="&quot;&gt;&lt;x&gt;"', $html);
    }

    public function testInputRadioChecksOnMatchingValue(): void
    {
        $radio = new InputRadio('size', 'l');
        $radio->setValue('l');

        $this->assertTrue($radio->isChecked());
    }
}
