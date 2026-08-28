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
use Kavalhub\Example\Presentation\Http\DemoPreferences;
use Kavalhub\Example\Presentation\Web\Form\AddCategoryForm;
use Kavalhub\Example\Presentation\Web\Form\AddFacetForm;
use Kavalhub\Example\Presentation\Web\Form\AddProductForm;
use Kavalhub\Example\Presentation\Web\Form\FacetProductForm;
use Kavalhub\Example\Presentation\Web\Layout\DemoLayout;
use Kavalhub\Example\Presentation\Web\Layout\FilterLiveRegion;
use Kavalhub\Example\Presentation\Web\Render\FormRenderer;
use Kavalhub\FormGenerator\Ajax\AjaxReplaceItem;
use Kavalhub\FormGenerator\Ajax\AjaxRequest;
use Kavalhub\FormGenerator\Ajax\AjaxResponse;
use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;
use Throwable;

final class DemoAjaxController
{
    public function __construct(
        private readonly CatalogRepositoryInterface $repository,
        private readonly DemoPreferences $preferences,
        private readonly DemoLayout $layout,
        private readonly FormRenderer $renderer,
        private readonly FilterLiveRegion $filterLiveRegion,
        private readonly ElementValidatorInterface $validator,
        private readonly ElementAjaxHandler $ajaxHandler,
        private readonly AddCategory $addCategory,
        private readonly AddFacet $addFacet,
        private readonly AddProduct $addProduct,
        private readonly DeleteCategory $deleteCategory,
        private readonly DeleteFacet $deleteFacet,
        private readonly DeleteProduct $deleteProduct,
    ) {
    }

    public function handle(): string
    {
        if (!AjaxRequest::isXmlHttpRequest()) {
            http_response_code(400);

            return json_encode(['error' => 'XMLHttpRequest required'], JSON_UNESCAPED_UNICODE);
        }

        $this->preferences->bindFromRequest();
        $decoratorMode = $this->preferences->decoratorMode();

        $page = (string)($_POST['page'] ?? 'filter');
        if ($page === '') {
            $page = 'filter';
        }

        $renderForm = fn (?ElementInterface $element): string => $element === null
            ? ''
            : $this->renderer->html($element, $decoratorMode);

        $deleteEntity = (string)($_POST['delete_entity'] ?? '');
        $deleteId = (int)($_POST['delete_id'] ?? 0);
        if ($deleteEntity !== '' && $deleteId > 0) {
            return $this->handleDelete($deleteEntity, $deleteId);
        }

        return match ($page) {
            'filter' => $this->handleFilter($renderForm, $decoratorMode),
            'facet' => $this->handleFacet(),
            'category' => $this->handleCategory(),
            'product' => $this->handleProduct(),
            default => $this->errorResponse('Unknown request', 400),
        };
    }

    private function handleDelete(string $entity, int $id): string
    {
        try {
            match ($entity) {
                'category' => $this->deleteCategory->execute($id),
                'facet' => $this->deleteFacet->execute($id),
                'product' => $this->deleteProduct->execute($id),
                default => throw new InvalidArgumentException('Неизвестная сущность'),
            };
            $form = match ($entity) {
                'category' => new AddCategoryForm($this->repository, $this->validator),
                'facet' => new AddFacetForm($this->repository, $this->validator),
                'product' => new AddProductForm($this->repository, $this->validator),
                default => throw new InvalidArgumentException('Неизвестная сущность'),
            };
            $this->layout->formCardWrap($form);

            return $this->ajaxHandler->handleBlock($form->getTable())
                ->setMessage('Запись удалена')
                ->jsonEncode();
        } catch (Throwable $exception) {
            return $this->errorResponse($exception->getMessage(), 500);
        }
    }

    /**
     * @param callable(ElementInterface): string $renderForm
     */
    private function handleFilter(callable $renderForm, \Kavalhub\Example\Presentation\Http\DecoratorMode $decoratorMode): string
    {
        $form = new FacetProductForm($this->repository, $this->validator);

        $targetId = AjaxRequest::readTargetId();
        if ($targetId === $form->getShowCategoryCheckboxId()) {
            return $this->ajaxHandler->handleBlock($form->refreshCategoryGroup())->jsonEncode();
        }

        $form->applyFilter(true);

        $response = new AjaxResponse();
        $response->addReplace(new AjaxReplaceItem(
            FilterLiveRegion::REGION_ID,
            html: $this->filterLiveRegion->render($form, $renderForm, $decoratorMode),
        ));
        if ($form->isFiltered()) {
            $response->setMessage('Фильтр обновлён');
        }

        return $response->jsonEncode();
    }

    private function handleFacet(): string
    {
        $form = new AddFacetForm($this->repository, $this->validator);
        $this->layout->formCardWrap($form);

        if ($targetId = AjaxRequest::readTargetId()) {
            return $this->ajaxHandler->handleField($form, $targetId)->jsonEncode();
        }

        $submit = $form->getSubmit();
        if ($this->validator->checkSubmit($submit)) {
            if ($this->validator->handle($form)) {
                $this->addFacet->execute(new Facet((string)$form->getByName('name')->getValue()));
                $form = new AddFacetForm($this->repository, $this->validator);
                $this->layout->formCardWrap($form);

                return $this->ajaxHandler->handleBlock($form->getTable())
                    ->setMessage('Фасет добавлен')
                    ->jsonEncode();
            }

            return $this->ajaxHandler->handleForm($form)->jsonEncode();
        }

        return $this->errorResponse('Unknown request', 400);
    }

    private function handleCategory(): string
    {
        $form = new AddCategoryForm($this->repository, $this->validator);
        $this->layout->formCardWrap($form);

        $submit = $form->getSubmit();
        if ($this->validator->checkSubmit($submit)) {
            if ($this->validator->handle($form)) {
                $this->addCategory->execute(new Category(
                    (string)$form->getByName('name')->getValue(),
                    (int)$form->getByName('sort')->getValue(),
                ));
                $form = new AddCategoryForm($this->repository, $this->validator);
                $this->layout->formCardWrap($form);

                return $this->ajaxHandler->handleBlock($form->getTable())
                    ->setMessage('Категория добавлена')
                    ->jsonEncode();
            }

            return $this->ajaxHandler->handleForm($form)->jsonEncode();
        }

        return $this->errorResponse('Unknown request', 400);
    }

    private function handleProduct(): string
    {
        $form = new AddProductForm($this->repository, $this->validator);
        $this->layout->formCardWrap($form);

        $submit = $form->getSubmit();
        if ($this->validator->checkSubmit($submit)) {
            if ($this->validator->handle($form)) {
                $this->addProduct->execute($form->toProduct($this->repository));
                $form = new AddProductForm($this->repository, $this->validator);
                $this->layout->formCardWrap($form);

                return $this->ajaxHandler->handleBlock($form->getTable())
                    ->setMessage('Товар добавлен')
                    ->jsonEncode();
            }

            return $this->ajaxHandler->handleForm($form)->jsonEncode();
        }

        return $this->errorResponse('Unknown request', 400);
    }

    private function errorResponse(string $message, int $code): string
    {
        http_response_code($code);

        return json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    }
}
