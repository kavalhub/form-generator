<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\CategoryList;
use Kavalhub\Example\UseCase\ProductList;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Fabric\ElementFabric;
use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\Group;
use Kavalhub\FormGenerator\Form\InputCheckbox;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\Label;
use Kavalhub\FormGenerator\Form\Nav;
use Kavalhub\FormGenerator\Observer\ElementObserverInterface;
use Kavalhub\FormGenerator\Validator\ElementValidator;

class FacetProductForm extends Form
{
    private const NAME = 'fl';
    private const BUTTON_NAME = 'go';
    private const BUTTON_VALUE = 'Показать';

    private InputSubmit $submit;
    private InputCheckbox $showCategory;
    private ElementInterface $categoryGroup;
    private CategoryList $categoryList;
    private ProductList $productList;

    public function __construct(
        private readonly Storage $storage, private readonly ElementValidator $validator,
        private ElementObserverInterface $elementObserver
    ) {
        parent::__construct(self::NAME);
        $this->categoryList = new CategoryList($this->storage);
        $this->productList = new ProductList($this->storage);
        $this->submit = (new InputSubmit(self::BUTTON_NAME))->setDefaultValue(self::BUTTON_VALUE);
        $this->showCategory = (new InputCheckbox('s', 'c'));

        $this->categoryGroup = $this->getElementCategory();

        $this->setNovalidate()
            ->addElement(
                (new Nav('cn'))->addElement(
                    (new Label('cat'))->setLabel('- Категория -')
                        ->setElement($this->categoryGroup)
                )
                    ->addElement($this->showCategory)
            )
            ->addElement($this->categoryGroup)
            ->addCallbackValidator(static function (Form $form) {
                $category = $form->getByName('gc');
                if (empty($category->getValueArray())) {
                    $form->setValid(false);
                    $category->addClass(['border-danger']);
                    $category->addError(['Укажите категорию']);
                    return false;
                }

                return true;
            })
            ->addElement($this->submit);
    }

    public function validate(): bool
    {
        $this->validator->handle($this->showCategory);
        $this->removeElement($this->submit);
        $this->removeElement($this->categoryGroup);
        $this->addElement($this->getElementCategory());
        $this->addElement($this->submit);
        if ($this->validator->checkSubmit($this->submit) && $this->validator->handle($this)) {
            $this->addElementFacet();
            return true;
        }
        return false;
    }

    private function getElementCategory(): ElementInterface
    {
        if ($this->showCategory->isChecked()) {
            $this->categoryList->addFilter(['tpc.category_id IS NOT NULL']);
        }
        return ElementFabric::create([
            ElementFabric::ELEMENT => Group::class,
            ElementFabric::NAME => 'gc',
            ElementFabric::METHOD => [
                [
                    'addClass' => [
                        'border',
                        'rounded-2',
                    ],
                ],
                [
                    ElementFabric::ADD_ELEMENT_BLOCK => [
                        ElementFabric::ELEMENT => InputCheckbox::class,
                        ElementFabric::BLOCK => array_map(static function ($category) {
                            return [
                                ElementFabric::NAME => 'cat',
                                ElementFabric::METHOD => [
                                    [
                                        'setDefaultValue' => (string)$category['id'],
                                        'setLabel' => $category['name'] . ' - ' . $category['count'],
                                        'setDisabled' => !$category['count'],
                                    ],
                                ],
                            ];
                        }, $this->categoryList->__toArray()),
                    ],
                ],
            ],
        ]);
    }

    private function addElementFacet(): void
    {
        if (!empty($value = $this->categoryGroup->getValueArray())) {
            $this->productList->addFilter(['tpc.category_id IN (' . implode(',', $value['cat']) . ')']);
        }
        $this->removeElement($this->submit);
        foreach ($this->productList->getFacet() as $key => $facet) {
            $group = ElementFabric::create([
                ElementFabric::ELEMENT => Group::class,
                ElementFabric::NAME => 'g' . $key,
                ElementFabric::METHOD => [
                    [
                        'addClass' => [
                            'border',
                            'rounded-2',
                        ],
                        ElementFabric::ATTACH_OBSERVER => $this->elementObserver,
                    ],
                    [
                        ElementFabric::ADD_ELEMENT_BLOCK => [
                            ElementFabric::ELEMENT => ElementFabric::getClassName($facet[ElementFabric::ELEMENT]),
                            ElementFabric::BLOCK => array_map(static function ($facet, $count) use ($key) {
                                return [
                                    ElementFabric::NAME => $key,
                                    ElementFabric::METHOD => [
                                        [
                                            'setDefaultValue' => (string)$facet,
                                            'setLabel' => $facet . ' - ' . $count,
                                        ],
                                    ],
                                ];
                            }, array_keys($facet['value']), $facet['value']),
                        ],
                    ],
                ],
            ]);
            $this->addElement((new Label($key))->setLabel('- ' . $key . ' -'));
            $this->addElement($group);
        }
        $this->validator->handle($this);
        $this->addElement($this->submit)
            ->notify();

        $filter = $this->elementObserver->getQuery();
        if (!empty($filter)) {
            $this->productList->addFilter(['tp.id IN (' . $filter . ')']);
        }
    }

    public function getProductList(): ProductList
    {
        return $this->productList;
    }
}
