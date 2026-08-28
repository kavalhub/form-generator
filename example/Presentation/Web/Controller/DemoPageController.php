<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Controller;

use InvalidArgumentException;
use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Kavalhub\Example\Application\UseCase\AddCategory;
use Kavalhub\Example\Application\UseCase\AddFacet;
use Kavalhub\Example\Application\UseCase\AddProduct;
use Kavalhub\Example\Application\UseCase\DeleteCategory;
use Kavalhub\Example\Application\UseCase\DeleteFacet;
use Kavalhub\Example\Application\UseCase\DeleteProduct;
use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Domain\Facet;
use Kavalhub\Example\Presentation\Http\DecoratorMode;
use Kavalhub\Example\Presentation\Http\DemoPageView;
use Kavalhub\Example\Presentation\Http\DemoPreferences;
use Kavalhub\Example\Presentation\Web\Form\AddCategoryForm;
use Kavalhub\Example\Presentation\Web\Form\AddFacetForm;
use Kavalhub\Example\Presentation\Web\Form\AddProductForm;
use Kavalhub\Example\Presentation\Web\Form\FacetProductForm;
use Kavalhub\Example\Presentation\Web\Layout\DemoLayout;
use Kavalhub\Example\Presentation\Web\Layout\FilterLiveRegion;
use Kavalhub\Example\Presentation\Web\Render\FormRenderer;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;
use Throwable;

final class DemoPageController
{
    private const NAV_ITEMS = [
        'filter' => 'Фильтр товаров',
        'category' => 'Категории',
        'facet' => 'Фасеты',
        'product' => 'Товары',
    ];

    public function __construct(
        private readonly CatalogRepositoryInterface $repository,
        private readonly DemoPreferences $preferences,
        private readonly DemoLayout $layout,
        private readonly FormRenderer $renderer,
        private readonly FilterLiveRegion $filterLiveRegion,
        private readonly ElementValidatorInterface $validator,
        private readonly AddCategory $addCategory,
        private readonly AddFacet $addFacet,
        private readonly AddProduct $addProduct,
        private readonly DeleteCategory $deleteCategory,
        private readonly DeleteFacet $deleteFacet,
        private readonly DeleteProduct $deleteProduct,
    ) {
    }

    public function handle(): DemoPageView
    {
        $this->preferences->bindFromRequest();
        $decoratorMode = $this->preferences->decoratorMode();
        $transport = $this->preferences->transport();

        $page = (string)($_GET['page'] ?? 'filter');
        $message = '';
        $form = null;

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $deleteId = (int)$_GET['delete'];
            try {
                match ($page) {
                    'category' => $this->deleteCategory->execute($deleteId),
                    'facet' => $this->deleteFacet->execute($deleteId),
                    'product' => $this->deleteProduct->execute($deleteId),
                    default => throw new InvalidArgumentException('Удаление недоступно для этой страницы'),
                };
                header('Location: ?' . http_build_query(array_merge(
                    $this->preferences->queryParams(),
                    ['page' => $page, 'deleted' => '1'],
                )));
                exit;
            } catch (Throwable $exception) {
                $message = 'Ошибка удаления: ' . $exception->getMessage();
            }
        }

        if (isset($_GET['deleted'])) {
            $message = 'Запись удалена';
        }

        try {
            switch ($page) {
                case 'category':
                    $form = new AddCategoryForm($this->repository, $this->validator);
                    $this->layout->formCardWrap($form);
                    if ($form->validate()) {
                        $this->addCategory->execute(new Category(
                            (string)$form->getByName('name')->getValue(),
                            (int)$form->getByName('sort')->getValue(),
                        ));
                        $message = 'Категория добавлена';
                        $form = new AddCategoryForm($this->repository, $this->validator);
                    }
                    break;
                case 'facet':
                    $form = new AddFacetForm($this->repository, $this->validator);
                    $this->layout->formCardWrap($form);
                    if ($form->validate()) {
                        $this->addFacet->execute(new Facet((string)$form->getByName('name')->getValue()));
                        $message = 'Фасет добавлен';
                        $form = new AddFacetForm($this->repository, $this->validator);
                    }
                    break;
                case 'product':
                    $form = new AddProductForm($this->repository, $this->validator);
                    $this->layout->formCardWrap($form);
                    if ($form->validate()) {
                        $this->addProduct->execute($form->toProduct($this->repository));
                        $message = 'Товар добавлен';
                        $form = new AddProductForm($this->repository, $this->validator);
                    }
                    break;
                default:
                    $page = 'filter';
                    $form = new FacetProductForm($this->repository, $this->validator);
                    if ($form->hasFilterInputInRequest()) {
                        $form->applyFilter(true);
                        if ($form->isFiltered()) {
                            $message = 'Фильтр применён';
                        }
                    } elseif ($form->validate()) {
                        $message = 'Фильтр применён';
                    }
                    break;
            }
        } catch (Throwable $exception) {
            $message = 'Ошибка: ' . $exception->getMessage();
        }

        $renderForm = fn (?ElementInterface $element): string => $element === null
            ? ''
            : $this->renderer->html($element, $decoratorMode);

        $settingsHtml = in_array($page, ['filter', 'category', 'facet', 'product'], true)
            ? $this->layout->settings($decoratorMode, $this->preferences->createSettingsForm())
            : '';

        $mainHtml = $page === 'filter'
            ? $this->filterLiveRegion->render($form, $renderForm, $decoratorMode)
            : $this->layout->formCard($form, $decoratorMode);

        return new DemoPageView(
            page: $page,
            decoratorMode: $decoratorMode,
            transport: $transport,
            message: $message,
            headerHtml: $this->layout->header($decoratorMode),
            toolbarHtml: $this->layout->toolbar($decoratorMode),
            navHtml: $this->layout->nav($page, $decoratorMode, self::NAV_ITEMS),
            flashHtml: $this->layout->flash($message, $decoratorMode),
            settingsHtml: $settingsHtml,
            mainHtml: $mainHtml,
            bodyClass: $this->bodyClass($decoratorMode),
            navItems: self::NAV_ITEMS,
        );
    }

    private function bodyClass(DecoratorMode $decoratorMode): string
    {
        return match ($decoratorMode) {
            DecoratorMode::Blade => 'decorator-blade',
            DecoratorMode::Twig => 'decorator-twig',
            DecoratorMode::Tailwind => 'decorator-tailwind bg-slate-50',
            DecoratorMode::Bootstrap, DecoratorMode::Html => 'bg-light',
        };
    }
}
