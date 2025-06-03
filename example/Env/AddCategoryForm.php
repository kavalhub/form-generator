<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\CategoryList;
use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\InputNumber;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Form\Label;
use Kavalhub\FormGenerator\Table\Table;
use Kavalhub\FormGenerator\Table\Td;
use Kavalhub\FormGenerator\Table\Tr;
use Kavalhub\FormGenerator\Validator\ElementValidator;

class AddCategoryForm extends Form
{
    private const NAME = 'add';

    private CategoryList $categoryList;
    private InputSubmit $submit;

    public function __construct(private readonly Storage $storage, private readonly ElementValidator $validator)
    {
        parent::__construct(self::NAME);
        $this->categoryList = (new CategoryList($this->storage));
        $this->submit = (new InputSubmit('addCategory'))->setDefaultValue('Добавить');

        $this->setNovalidate()
            ->addElement((new Label(''))->setLabel('<h3>Добавление категории</h3>'))
            ->addElement(
                (new InputText('name'))->setRequired()
                    ->setPlaceholder('Введите название категории')
                    ->addCallbackValidator(function (InputText $name) {
                        $test = $this->categoryList->addFilter(['tf.name' => $name->getValue()]);

                        return true;
                    })
            )
            ->addElement(
                (new InputNumber('sort'))->setRequired()
                    ->setPlaceholder('Введите порядковый номер')
                    ->setMin(1)
                    ->setMax(count($this->categoryList->__toArray()) + 1)
            )
            ->addElement($this->submit)
            ->addElement($this->getTable());
    }

    private function getTable(): Table
    {
        $table = (new Table());
        foreach ($this->categoryList->__toArray() as $category) {
            $table->addElement(
                (new Tr())->addElement(new Td((string)$category['sort']))
                    ->addElement(new Td($category['name']))
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
