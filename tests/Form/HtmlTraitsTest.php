<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Form;

use Kavalhub\FormGenerator\Form\Button;
use Kavalhub\FormGenerator\Form\InputCheckbox;
use Kavalhub\FormGenerator\Form\InputFile;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Form\Select;
use Kavalhub\FormGenerator\Form\Textarea;
use PHPUnit\Framework\TestCase;

final class HtmlTraitsTest extends TestCase
{
    public function testInputTextRendersReadonlyAutocompleteAndAutofocus(): void
    {
        $input = (new InputText('email'))
            ->setReadonly()
            ->setAutocomplete('email')
            ->setAutofocus();

        $html = $input->getHtml();

        $this->assertStringContainsString(' readonly', $html);
        $this->assertStringContainsString('autocomplete="email"', $html);
        $this->assertStringContainsString(' autofocus', $html);
    }

    public function testInputTextRendersMinlength(): void
    {
        $input = (new InputText('code'))->setMinlength(3);
        $html = $input->getHtml();

        $this->assertStringContainsString('minlength="3"', $html);
    }

    public function testTextareaRendersRowsAndCols(): void
    {
        $textarea = (new Textarea('desc'))->setRows(5)->setCols(40);
        $html = $textarea->getHtml();

        $this->assertStringContainsString('rows="5"', $html);
        $this->assertStringContainsString('cols="40"', $html);
    }

    public function testInputFileRendersAcceptAndMultiple(): void
    {
        $input = (new InputFile('photo'))
            ->setAccept('image/*')
            ->setMultiple();

        $html = $input->getHtml();

        $this->assertStringContainsString('accept="image/*"', $html);
        $this->assertStringContainsString(' multiple', $html);
        $this->assertStringContainsString('name="photo[]"', $html);
    }

    public function testSelectRendersDisabledAndSize(): void
    {
        $select = (new Select('country'))
            ->setDisabled()
            ->setSize(4);

        $html = $select->getHtml();

        $this->assertStringContainsString(' disabled', $html);
        $this->assertStringContainsString('size="4"', $html);
    }

    public function testCheckboxUsesArrayNameWithoutMultipleAttribute(): void
    {
        $checkbox = new InputCheckbox('agree', 'yes');
        $html = $checkbox->getHtml();

        $this->assertStringContainsString('name="agree[]"', $html);
        $this->assertStringNotContainsString(' multiple', $html);
    }

    public function testButtonRendersType(): void
    {
        $button = (new Button('send'))->setLabel('Go')->setType('button');
        $html = $button->getHtml();

        $this->assertStringContainsString('type="button"', $html);
        $this->assertStringContainsString('>Go</button>', $html);
    }

    public function testListAttributeIsEscaped(): void
    {
        $input = (new InputText('q'))->setList('"><x>');
        $html = $input->getHtml();

        $this->assertStringContainsString('list="&quot;&gt;&lt;x&gt;"', $html);
    }
}
