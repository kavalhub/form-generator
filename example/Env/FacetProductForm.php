<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\UseCase\CategoryList;
use Kavalhub\Example\UseCase\ProductList;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Event\ElementChangedEvent;
use Kavalhub\FormGenerator\Event\ElementEventDispatcher;
use Kavalhub\FormGenerator\Factory\ElementFactory;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Nav;
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
    private FacetSelectionListener $facetSelection;

    /** @var array{categories: string[], facets: array<string, string[]>} */
    private array $appliedFilters = [
        'categories' => [],
        'facets' => [],
    ];

    public function __construct(
        private readonly Storage $storage,
        private readonly ElementValidatorInterface $validator,
        ?ElementEventDispatcher $dispatcher = null,
        ?FacetSelectionListener $facetSelection = null,
    ) {
        parent::__construct(self::NAME);
        $this->dispatcher = $dispatcher ?? new ElementEventDispatcher();
        $this->facetSelection = $facetSelection ?? new FacetSelectionListener($this->storage);
        $this->dispatcher->addListener(
            ElementChangedEvent::class,
            $this->facetSelection->onElementChanged(...),
        );

        $this->categoryList = new CategoryList($this->storage);
        $this->productList = new ProductList($this->storage);
        $this->submit = (new InputSubmit(self::BUTTON_NAME))->setDefaultValue(self::BUTTON_VALUE);
        $this->showCategory = (new InputCheckbox('s', 'c'))->addClass(['js-show-category']);

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
        return $this->applyFilter(false);
    }

    public function applyFilter(bool $force = false): bool
    {
        $this->filtered = false;
        $this->appliedFilters = ['categories' => [], 'facets' => []];

        $this->validator->handle($this->showCategory);
        $this->removeElement($this->submit);
        $this->removeElement($this->categoryGroup);
        $this->categoryGroup = $this->getElementCategory();
        $this->addElement($this->categoryGroup);
        $this->addElement($this->submit);
        if (($force || $this->validator->checkSubmit($this->submit)) && $this->validator->handle($this)) {
            $this->addElementFacet();

            return true;
        }

        return false;
    }

    public function getSubmit(): InputSubmit
    {
        return $this->submit;
    }

    public function getShowCategoryCheckboxId(): string
    {
        return $this->showCategory->getId();
    }

    /**
     * Перестроить список категорий (показать/скрыть пустые) без применения фильтра.
     */
    public function refreshCategoryGroup(): ElementInterface
    {
        $this->categoryList = new CategoryList($this->storage);
        $this->validator->handle($this->showCategory);
        $group = $this->getElementCategory();
        $group->setParent($this);
        $this->validator->handle($group);

        return $group;
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

    public function getDispatcher(): ElementEventDispatcher
    {
        return $this->dispatcher;
    }

    public function getFacetSelection(): FacetSelectionListener
    {
        return $this->facetSelection;
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
        $this->facetSelection->reset();

        $categoryValues = $this->getByName('gc')->getValueArray()['cat'] ?? [];
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
                        ElementFactory::SET_DISPATCHER => $this->dispatcher,
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
        $this->addElement($this->submit);
        if ($this->hasCheckedFacet()) {
            $this->notify();

            if ($this->facetSelection->hasSelection()) {
                $this->appliedFilters['facets'] = $this->facetSelection->getFacetList();
                $productIds = $this->facetSelection->getProductIds();
                if ($productIds === []) {
                    $this->productList->addRawFilter('1 = 0');
                } else {
                    $this->productList->addProductIdsFilter($productIds);
                }
            }
        }
    }

    private function hasCheckedFacet(): bool
    {
        foreach ($this->productList->getFacet() as $key => $_facet) {
            $group = $this->getByName('g' . $key);
            if (!$group->getComposite()) {
                continue;
            }
            foreach ($group->getComposite()->getAll() as $child) {
                if (method_exists($child, 'isChecked') && $child->isChecked()) {
                    return true;
                }
            }
        }

        return false;
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
