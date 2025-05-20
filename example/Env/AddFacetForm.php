<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\FacetList;
use Kavalhub\FormGenerator\Form\Datalist;
use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Form\Label;
use Kavalhub\FormGenerator\Table\Table;
use Kavalhub\FormGenerator\Table\Td;
use Kavalhub\FormGenerator\Table\Tr;
use Kavalhub\FormGenerator\Validator\ElementValidator;

class AddFacetForm extends Form
{
    private const NAME = 'add';
    private const BUTTON_NAME = 'addFacet';
    private const BUTTON_VALUE = 'Добавить';
    private const BUTTON_CLASS = ['js-button-ajax'];
    private const LABEL = '<h3>Добавление фасета</h3>';

    private FacetList $facetList;
    private InputSubmit $submit;

    public function __construct(private readonly Storage $storage, private ElementValidator $validator)
    {
        parent::__construct(self::NAME);
        $this->facetList = new FacetList($this->storage);
        $this->submit = (new InputSubmit(self::BUTTON_NAME))->setDefaultValue(self::BUTTON_VALUE)
            ->addClass(self::BUTTON_CLASS);

        $input = (new InputText('name'))->setRequired()
            ->addClass(['js-on-input'])
            ->setPlaceholder('Введите название фасета')
            ->addCallbackValidator(function (InputText $name) {
                $test = $this->facetList->addFilter(['tf.name' => $name->getValue()]);

                return true;
            });

        $this->setNovalidate()
            ->addElement((new Label(''))->setLabel(self::LABEL))
            ->addElement($input)
            ->addElement(new Datalist($input))
            ->addElement($this->submit)
            ->addElement($this->getTable());
    }

    private function getTable(): Table
    {
        $table = (new Table());
        foreach ($this->facetList->__toArray() as $item) {
            $table->addElement(
                (new Tr())->addElement(new Td($item['name']))
                    ->addElement(new Td((string)$item['count']))
            );
        }

        return $table;
    }

    public function validate(): bool
    {
        if ($this->validator->checkSubmit($this->submit) && $this->validator->handle($this)) {
            return true;
        }
        return false;
    }
}
