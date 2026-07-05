<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\CategoryList;
use Kavalhub\Example\UseCase\FacetList;
use Kavalhub\Example\UseCase\ProductList;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputNumber;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Select;
use Kavalhub\FormGenerator\Html\Table\Table;
use Kavalhub\FormGenerator\Html\Table\Td;
use Kavalhub\FormGenerator\Html\Table\Tr;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class AddProductForm extends Form
{
    private const NAME = 'addProduct';

    private CategoryList $categoryList;
    private FacetList $facetList;
    private ProductList $productList;
    private InputSubmit $submit;

    public function __construct(
        private readonly Storage $storage,
        private readonly ElementValidatorInterface $validator,
    ) {
        parent::__construct(self::NAME);
        $this->categoryList = new CategoryList($this->storage);
        $this->facetList = new FacetList($this->storage);
        $this->productList = new ProductList($this->storage);
        $this->submit = (new InputSubmit('submit'))->setDefaultValue('Добавить');

        $this->setMethod('post')
            ->setNovalidate()
            ->addElement((new Label(''))->setLabel('<h3>Добавление товара</h3>')->setAllowHtml())
            ->addElement(
                (new InputText('name'))->setRequired()
                    ->setPlaceholder('Введите название товара')
            )
            ->addElement(
                (new InputNumber('price'))->setRequired()
                    ->setPlaceholder('Цена')
                    ->setMin(0)
                    ->setStep(0.01)
            )
            ->addElement($this->buildCategorySelect());

        foreach ($this->facetList->__toArray() as $facet) {
            $fieldName = 'facet_' . $facet['id'];
            $this->addElement(
                (new Label($fieldName))->setLabel($facet['name'])
            );
            $this->addElement(
                (new InputText($fieldName))->setRequired()
                    ->setPlaceholder('Значение: ' . $facet['name'])
            );
        }

        $this->addElement($this->submit)
            ->addElement($this->getTable());
    }

    private function buildCategorySelect(): Select
    {
        $items = ['' => '— выберите категорию —'];
        foreach ($this->categoryList->__toArray() as $category) {
            $items[(string)$category['id']] = $category['name'];
        }

        return (new Select('category', $items))->setRequired();
    }

    private function getTable(): Table
    {
        $table = new Table();
        foreach ($this->productList->__toArray() as $id => $product) {
            $table->addElement(
                (new Tr())
                    ->addElement(new Td((string)$id))
                    ->addElement(new Td((string)$product['name']))
                    ->addElement(new Td((string)($product['price'] ?? '')))
            );
        }

        return $table;
    }

    /**
     * @return array<int, string>
     */
    public function getFacetValues(): array
    {
        $values = [];
        foreach ($this->facetList->__toArray() as $facet) {
            $field = $this->getByName('facet_' . $facet['id']);
            if (method_exists($field, 'getValue')) {
                $values[(int)$facet['id']] = (string)$field->getValue();
            }
        }

        return $values;
    }

    public function validate(): bool
    {
        if ($this->validator->checkSubmit($this->submit) && $this->validator->handle($this)) {
            return true;
        }

        return false;
    }
}
