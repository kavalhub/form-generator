<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use InvalidArgumentException;
use Kavalhub\Example\Domain\Product;
use Kavalhub\Example\Domain\Product\ProductFacet;
use Kavalhub\Example\Domain\Product\ProductFacetCollection;
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
    public const TABLE_ID = 'products';

    private const NAME = 'addProduct';

    private CategoryList $categoryList;
    private FacetList $facetList;
    private ProductList $productList;
    private InputSubmit $submit;
    private Table $table;

    public function __construct(
        private readonly Storage $storage,
        private readonly ElementValidatorInterface $validator,
    ) {
        parent::__construct(self::NAME);
        $this->categoryList = new CategoryList($this->storage);
        $this->facetList = new FacetList($this->storage);
        $this->productList = new ProductList($this->storage);
        $this->submit = (new InputSubmit('submit'))->setDefaultValue('Добавить');

        $this->table = $this->buildTable();
        $this->table->setId(self::TABLE_ID);

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
                (new Label('l_' . $fieldName))->setLabel($facet['name'])
            );
            $this->addElement(
                (new InputText($fieldName))
                    ->setPlaceholder('Значение: ' . $facet['name'] . ' (необязательно)')
            );
        }

        $this->addElement($this->submit)
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

    private function buildCategorySelect(): Select
    {
        $items = ['' => '— выберите категорию —'];
        foreach ($this->categoryList->__toArray() as $category) {
            $items[(string)$category['id']] = $category['name'];
        }

        return (new Select('category', $items))->setRequired();
    }

    private function buildTable(): Table
    {
        $table = new Table();
        foreach ($this->productList->__toArray() as $id => $product) {
            $table->addElement(
                (new Tr())
                    ->addElement(new Td((string)$id))
                    ->addElement(new Td((string)$product['name']))
                    ->addElement(new Td((string)($product['price'] ?? '')))
                    ->addElement((new Td(RowDeleteLink::create('product', (int)$id)))->setAllowHtml())
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
            if (!method_exists($field, 'getValue')) {
                continue;
            }
            $value = trim((string)$field->getValue());
            if ($value === '') {
                continue;
            }
            $values[(int)$facet['id']] = $value;
        }

        return $values;
    }

    public function toProduct(Storage $storage): Product
    {
        $categoryId = (int)$this->getByName('category')->getValue();
        $category = $storage->getCategoryById($categoryId);
        if ($category === null) {
            throw new InvalidArgumentException('Unknown category id: ' . $categoryId);
        }

        $facets = new ProductFacetCollection();
        foreach ($this->getFacetValues() as $facetId => $value) {
            $facet = $storage->getFacetById((int)$facetId);
            if ($facet === null) {
                continue;
            }
            $facets->add(new ProductFacet($facet->getName(), $value));
        }

        return new Product(
            (string)$this->getByName('name')->getValue(),
            (float)$this->getByName('price')->getValue(),
            $category,
            $facets,
        );
    }

    public function validate(): bool
    {
        if ($this->validator->checkSubmit($this->submit) && $this->validator->handle($this)) {
            return true;
        }

        return false;
    }
}
