<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Form;

use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Kavalhub\Example\Application\UseCase\FacetList;
use Kavalhub\Example\Presentation\Web\Layout\RowDeleteLink;
use Kavalhub\FormGenerator\Html\Datalist;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Table\Table;
use Kavalhub\FormGenerator\Html\Table\Td;
use Kavalhub\FormGenerator\Html\Table\Tr;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class AddFacetForm extends Form
{
    public const TABLE_ID = 'facets';

    private const NAME = 'add';
    private const BUTTON_NAME = 'addFacet';
    private const BUTTON_VALUE = 'Добавить';
    private const LABEL = '<h3>Добавление фасета</h3>';

    private FacetList $facetList;
    private InputSubmit $submit;
    private Table $table;

    public function __construct(private readonly CatalogRepositoryInterface $repository, private readonly ElementValidatorInterface $validator)
    {
        parent::__construct(self::NAME);
        $this->facetList = new FacetList($this->repository);
        $this->submit = (new InputSubmit(self::BUTTON_NAME))->setDefaultValue(self::BUTTON_VALUE)
            ->setAjax();

        $input = (new InputText('name'))->setRequired()
            ->setAjax()
            ->setPlaceholder('Введите название фасета')
            ->addCallbackValidator(function (InputText $name) {
                $this->facetList->addNameFilter($name->getValue());

                return true;
            });

        $this->table = $this->buildTable();
        $this->table->setId(self::TABLE_ID);

        $this->setMethod('post')
            ->setNovalidate()
            ->addElement((new Label(''))->setLabel(self::LABEL)->setAllowHtml())
            ->addElement($input)
            ->addElement(new Datalist($input))
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
        foreach ($this->facetList->__toArray() as $item) {
            $table->addElement(
                (new Tr())
                    ->addElement(new Td($item['name']))
                    ->addElement(new Td((string)$item['count']))
                    ->addElement((new Td(RowDeleteLink::create('facet', (int)$item['id'])))->setAllowHtml())
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
