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

class AddProductForm extends Form
{
    public function __construct(Storage $storage, string $name)
    {
        parent::__construct($name);

        $this->setNovalidate()
            ->addElement((new Label(''))->setLabel('<h3>Добавление категории</h3>'))
            ->addElement(
                (new InputText('name'))->setRequired()
                    ->setPlaceholder('Введите название категории')
            )
            ->addElement(
                (new InputNumber('sort'))->setRequired()
                    ->setPlaceholder('Введите порядковый номер')
                    ->setMin(1)
                    ->setMax(count($categoryList) + 1)
            )
            ->addElement((new InputSubmit('addCategory'))->setDefaultValue('Добавить'))
            ->addElement($this->getTable($categoryList));
    }

    private function getTable($categoryList): Table
    {
        $table = (new Table());
        foreach ($categoryList as $category) {
            $table->addElement(
                (new Tr())->addElement(new Td((string)$category['sort']))
                    ->addElement(new Td($category['name']))
            );
        }

        return $table;
    }
}