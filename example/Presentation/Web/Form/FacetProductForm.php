<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Form;

use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Kavalhub\Example\Application\UseCase\CategoryList;
use Kavalhub\Example\Application\UseCase\ProductList;
use Kavalhub\Example\Presentation\Web\Render\FormRenderer;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Event\ElementChangedEvent;
use Kavalhub\FormGenerator\Event\ElementEventDispatcher;
use Kavalhub\FormGenerator\Factory\ElementFactory;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use Kavalhub\FormGenerator\Html\InputRange;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Nav;
use Kavalhub\FormGenerator\Html\Paginator;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class FacetProductForm extends Form
{
    private const NAME = 'fl';
    private const BUTTON_NAME = 'go';
    private const BUTTON_VALUE = 'Показать';
    private const PAGINATOR_NAME = 'pn';
    private const PAGINATOR_LIMIT = 5;
    private const PRICE_GROUP_NAME = 'gp';

    private InputSubmit $submit;
    private InputCheckbox $showCategory;
    private ElementInterface $categoryGroup;
    private CategoryList $categoryList;
    private ProductList $productList;
    private bool $filtered = false;
    private FacetSelectionListener $facetSelection;
    private ?Paginator $paginator = null;

    /** @var array{categories: string[], facets: array<string, string[]>, price: ?array{min: float, max: float, currency: string}} */
    private array $appliedFilters = [
        'categories' => [],
        'facets' => [],
        'price' => null,
    ];

    public function __construct(
        private readonly CatalogRepositoryInterface $repository,
        private readonly ElementValidatorInterface $validator,
        ?ElementEventDispatcher $dispatcher = null,
        ?FacetSelectionListener $facetSelection = null,
    ) {
        parent::__construct(self::NAME);
        $this->dispatcher = $dispatcher ?? new ElementEventDispatcher();
        $this->facetSelection = $facetSelection ?? new FacetSelectionListener($this->repository);
        $this->dispatcher->addListener(
            ElementChangedEvent::class,
            $this->facetSelection->onElementChanged(...),
        );

        $this->categoryList = new CategoryList($this->repository);
        $this->productList = new ProductList($this->repository);
        $this->submit = (new InputSubmit(self::BUTTON_NAME))->setDefaultValue(self::BUTTON_VALUE);
        $this->showCategory = (new InputCheckbox('s', 'c'))->addClass(['js-show-category']);

        $this->categoryGroup = $this->getElementCategory();

        $this->setMethod('get')
            ->setNovalidate()
            ->setAjax(true)
            ->setUrlState('replaceState')
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

    public function hasFilterInputInRequest(): bool
    {
        $prefix = self::NAME . '_';
        foreach (array_keys($_REQUEST) as $key) {
            if (!str_starts_with($key, $prefix)) {
                continue;
            }
            $rest = substr($key, strlen($prefix));
            if ($rest === 'go' || str_starts_with($rest, 'go[')) {
                continue;
            }
            if (str_starts_with($rest, 'gc') || str_starts_with($rest, 'cn_')) {
                return true;
            }
            if (str_starts_with($rest, self::PRICE_GROUP_NAME)) {
                return true;
            }
            if (str_starts_with($rest, 'g')) {
                return true;
            }
            if (str_starts_with($rest, 'pn_')) {
                return true;
            }
        }

        return false;
    }

    public function applyFilter(bool $force = false): bool
    {
        $this->filtered = false;
        $this->appliedFilters = ['categories' => [], 'facets' => [], 'price' => null];
        $this->productList = new ProductList($this->repository);

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
        $this->categoryList = new CategoryList($this->repository);
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
     * @return array{categories: string[], facets: array<string, string[]>, price: ?array{min: float, max: float, currency: string}}
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

        $bounds = $this->productList->getPriceBounds();
        [$selectedMin, $selectedMax, $priceNarrowed] = $this->resolvePriceSelection($bounds);
        if ($priceNarrowed) {
            $this->productList->addPriceRangeFilter($selectedMin, $selectedMax);
            $this->appliedFilters['price'] = [
                'min' => $selectedMin,
                'max' => $selectedMax,
                'currency' => $bounds['currency'],
            ];
        }

        $this->removeElement($this->submit);
        $this->addPriceGroup($bounds, $selectedMin, $selectedMax);
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
            if ($key === 'Бренд') {
                $group->setPath(FormRenderer::BRAND_GROUP_TEMPLATE_BASE);
            }
            $this->addElement((new Label($key))->setLabel('- ' . $key . ' -'));
            $this->addElement($group);
        }
        $this->validator->handle($this);
        $this->addElement($this->submit);
        if ($this->hasCheckedFacet()) {
            foreach ($this->productList->getFacet() as $key => $_facet) {
                $group = $this->getByName('g' . $key);
                if ($group->getComposite()) {
                    $group->notify();
                }
            }

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

        $this->bindPaginator();
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
        $list = new CategoryList($this->repository);
        foreach ($list->__toArray() as $category) {
            if (in_array((string)$category['id'], $ids, true)) {
                $names[] = (string)$category['name'];
            }
        }

        return $names;
    }

    /**
     * @param array{min: float, max: float, currency: string} $bounds
     * @return array{0: float, 1: float, 2: bool}
     */
    private function resolvePriceSelection(array $bounds): array
    {
        $minBound = $bounds['min'];
        $maxBound = $bounds['max'];
        if ($minBound > $maxBound) {
            return [$minBound, $maxBound, false];
        }

        $prefix = self::NAME . '_' . self::PRICE_GROUP_NAME . '_';
        $requestedMin = isset($_REQUEST[$prefix . 'min']) ? (float)$_REQUEST[$prefix . 'min'] : $minBound;
        $requestedMax = isset($_REQUEST[$prefix . 'max']) ? (float)$_REQUEST[$prefix . 'max'] : $maxBound;

        $selectedMin = max($minBound, min($requestedMin, $maxBound));
        $selectedMax = max($minBound, min($requestedMax, $maxBound));
        if ($selectedMin > $selectedMax) {
            $selectedMin = $selectedMax;
        }

        $step = $this->priceStep($minBound, $maxBound);
        $narrowed = abs($selectedMin - $minBound) > ($step / 2)
            || abs($selectedMax - $maxBound) > ($step / 2);

        return [$selectedMin, $selectedMax, $narrowed];
    }

    /**
     * @param array{min: float, max: float, currency: string} $bounds
     */
    private function addPriceGroup(array $bounds, float $min, float $max): void
    {
        if ($bounds['min'] > $bounds['max']) {
            return;
        }

        $step = $this->priceStep($bounds['min'], $bounds['max']);
        $group = (new Group(self::PRICE_GROUP_NAME))
            ->setPath('layout/PriceFilter')
            ->addClass(['demo-price-filter'])
            ->addData(['currency' => $bounds['currency']]);
        $group->addElement(
            (new InputRange('min'))
                ->setMin($bounds['min'])
                ->setMax($bounds['max'])
                ->setStep($step)
                ->setValue((string)$min),
        );
        $group->addElement(
            (new InputRange('max'))
                ->setMin($bounds['min'])
                ->setMax($bounds['max'])
                ->setStep($step)
                ->setValue((string)$max),
        );
        $this->addElement((new Label('price'))->setLabel('- Цена -'));
        $this->addElement($group);
    }

    private function priceStep(float $min, float $max): float
    {
        $spread = $max - $min;
        if ($spread <= 0) {
            return 1.0;
        }

        return max(1.0, (float)round($spread / 100));
    }

    public function getProductList(): ProductList
    {
        return $this->productList;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->filtered ? $this->paginator : null;
    }

    private function bindPaginator(): void
    {
        $paginator = $this->ensurePaginator();
        $paginator->setParent($this);

        if ($this->shouldResetPaginatorPage()) {
            $paginator->getByName('page')->setValue('1');
        }

        $paginator
            ->bind($this->validator)
            ->setQueryList($this->buildPaginatorQueryList())
            ->setCount($this->productList->count());

        $this->paginator = $paginator;
    }

    private function shouldResetPaginatorPage(): bool
    {
        if ($this->validator->checkSubmit($this->submit)) {
            return true;
        }

        $prefix = self::NAME . '_' . self::PRICE_GROUP_NAME . '_';

        return isset($_REQUEST[$prefix . 'min']) || isset($_REQUEST[$prefix . 'max']);
    }

    private function ensurePaginator(): Paginator
    {
        if ($this->paginator === null) {
            $this->paginator = (new Paginator(self::PAGINATOR_NAME, self::PAGINATOR_LIMIT, 1))
                ->setAjax(true);
        }

        return $this->paginator;
    }

    /**
     * @return array<string, scalar|array<int|string, scalar|null>>
     */
    private function buildPaginatorQueryList(): array
    {
        $list = ['page' => 'filter'];
        $prefix = self::NAME . '_';
        $paginatorPrefix = $prefix . self::PAGINATOR_NAME . '_';
        $pageKey = $prefix . self::PAGINATOR_NAME . '_page';

        foreach ($_REQUEST as $key => $value) {
            if (str_starts_with($key, 'demoSettings_')) {
                $list[$key] = $value;
                continue;
            }
            if (!str_starts_with($key, $prefix) || str_starts_with($key, $paginatorPrefix)) {
                continue;
            }
            if ($key === $pageKey) {
                continue;
            }
            if ($key === $prefix . self::BUTTON_NAME || str_starts_with($key, $prefix . self::BUTTON_NAME . '[')) {
                continue;
            }
            $list[$key] = $value;
        }

        return $list;
    }
}
