<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\CategoryList;
use Kavalhub\Example\UseCase\ProductList;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Factory\ElementFactory;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Nav;
use Kavalhub\FormGenerator\Observer\ElementObserverInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

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
    private bool $filtered = false;

    /** @var array{categories: string[], facets: array<string, string[]>} */
    private array $appliedFilters = [
        'categories' => [],
        'facets' => [],
    ];

    public function __construct(
        private readonly Storage $storage,
        private readonly ElementValidatorInterface $validator,
        private ElementObserverInterface $elementObserver,
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
        $this->filtered = false;
        $this->appliedFilters = ['categories' => [], 'facets' => []];

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

    public function isFiltered(): bool
    {
        return $this->filtered;
    }

    /**
     * @return array{categories: string[], facets: array<string, string[]>}
     */
    public function getAppliedFilters(): array
    {
        return $this->appliedFilters;
    }

    private function getElementCategory(): ElementInterface
    {
        if ($this->showCategory->isChecked()) {
            $this->categoryList->addRawFilter('tpc.category_id IS NOT NULL');
        }

        return ElementFactory::create([
            ElementFactory::ELEMENT => Group::class,
            ElementFactory::NAME => 'gc',
            ElementFactory::METHOD => [
                [
                    'addClass' => [
                        'border',
                        'rounded-2',
                    ],
                ],
                [
                    ElementFactory::ADD_ELEMENT_BLOCK => [
                        ElementFactory::ELEMENT => InputCheckbox::class,
                        ElementFactory::BLOCK => array_map(static function ($category) {
                            return [
                                ElementFactory::NAME => 'cat',
                                ElementFactory::METHOD => [
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
        $this->filtered = true;
        $this->elementObserver->reset();

        $categoryValues = $this->categoryGroup->getValueArray()['cat'] ?? [];
        $this->appliedFilters['categories'] = $this->resolveCategoryNames($categoryValues);

        if ($categoryValues !== []) {
            $this->productList->addCategoryIdsFilter($categoryValues);
        }

        $this->removeElement($this->submit);
        foreach ($this->productList->getFacet() as $key => $facet) {
            $group = ElementFactory::create([
                ElementFactory::ELEMENT => Group::class,
                ElementFactory::NAME => 'g' . $key,
                ElementFactory::METHOD => [
                    [
                        'addClass' => [
                            'border',
                            'rounded-2',
                        ],
                        ElementFactory::ATTACH_OBSERVER => $this->elementObserver,
                    ],
                    [
                        ElementFactory::ADD_ELEMENT_BLOCK => [
                            ElementFactory::ELEMENT => ElementFactory::getClassName($facet[ElementFactory::ELEMENT]),
                            ElementFactory::BLOCK => array_map(static function ($facetValue, $count) use ($key) {
                                return [
                                    ElementFactory::NAME => $key,
                                    ElementFactory::METHOD => [
                                        [
                                            'setDefaultValue' => (string)$facetValue,
                                            'setLabel' => $facetValue . ' - ' . $count,
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

        if ($this->elementObserver->hasSelection()) {
            $this->appliedFilters['facets'] = $this->elementObserver->getFacetList();
            $productIds = $this->elementObserver->getProductIds();
            if ($productIds === []) {
                $this->productList->addRawFilter('1 = 0');
            } else {
                $this->productList->addProductIdsFilter($productIds);
            }
        }
    }

    /**
     * @param string[] $ids
     * @return string[]
     */
    private function resolveCategoryNames(array $ids): array
    {
        $names = [];
        $list = new CategoryList($this->storage);
        foreach ($list->__toArray() as $category) {
            if (in_array((string)$category['id'], $ids, true)) {
                $names[] = (string)$category['name'];
            }
        }

        return $names;
    }

    public function getProductList(): ProductList
    {
        return $this->productList;
    }
}
