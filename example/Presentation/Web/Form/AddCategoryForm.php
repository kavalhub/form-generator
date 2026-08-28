<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Form;

use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Kavalhub\Example\Application\UseCase\CategoryList;
use Kavalhub\Example\Presentation\Web\Layout\RowDeleteLink;
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
    public const TABLE_ID = 'categories';

    private const NAME = 'addCategory';

    private CategoryList $categoryList;
    private InputSubmit $submit;
    private Table $table;

    public function __construct(private readonly CatalogRepositoryInterface $repository, private readonly ElementValidatorInterface $validator)
    {
        parent::__construct(self::NAME);
        $this->categoryList = (new CategoryList($this->repository));
        $this->submit = (new InputSubmit('submit'))->setDefaultValue('Добавить')->setAjax();

        $this->table = $this->buildTable();
        $this->table->setId(self::TABLE_ID);

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
            ->addElement($this->table);
    }

    public function getSubmit(): InputSubmit
    {
        return $this->submit;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    private function buildTable(): Table
    {
        $table = new Table();
        foreach ($this->categoryList->__toArray() as $category) {
            $table->addElement(
                (new Tr())
                    ->addElement(new Td((string)$category['sort']))
                    ->addElement(new Td($category['name']))
                    ->addElement((new Td(RowDeleteLink::create('category', (int)$category['id'])))->setAllowHtml())
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
