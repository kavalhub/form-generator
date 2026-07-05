<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\CategoryList;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputNumber;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Table\Table;
use Kavalhub\FormGenerator\Html\Table\Td;
use Kavalhub\FormGenerator\Html\Table\Tr;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class AddCategoryForm extends Form
{
    private const NAME = 'addCategory';

    private CategoryList $categoryList;
    private InputSubmit $submit;

    public function __construct(private readonly Storage $storage, private readonly ElementValidatorInterface $validator)
    {
        parent::__construct(self::NAME);
        $this->categoryList = (new CategoryList($this->storage));
        $this->submit = (new InputSubmit('submit'))->setDefaultValue('Добавить');

        $this->setMethod('post')
            ->setNovalidate()
            ->addElement((new Label(''))->setLabel('<h3>Добавление категории</h3>')->setAllowHtml())
            ->addElement(
                (new InputText('name'))->setRequired()
                    ->setPlaceholder('Введите название категории')
                    ->addCallbackValidator(function (InputText $name) {
                        $this->categoryList->addNameFilter($name->getValue());

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
