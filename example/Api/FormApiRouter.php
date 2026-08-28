<?php
declare(strict_types=1);

namespace Kavalhub\Example\Api;

use InvalidArgumentException;
use Kavalhub\Example\Application\Port\CatalogRepositoryInterface;
use Kavalhub\Example\Application\UseCase\AddCategory;
use Kavalhub\Example\Application\UseCase\AddFacet;
use Kavalhub\Example\Application\UseCase\AddProduct;
use Kavalhub\Example\Application\UseCase\CategoryList;
use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Domain\Facet;
use Kavalhub\Example\Presentation\Web\Form\AddCategoryForm;
use Kavalhub\Example\Presentation\Web\Form\AddFacetForm;
use Kavalhub\Example\Presentation\Web\Form\AddProductForm;
use Kavalhub\Example\Presentation\Web\Form\FacetProductForm;
use Kavalhub\FormGenerator\Api\FormApiHandler;
use Kavalhub\FormGenerator\Api\FormApiResponse;
use Kavalhub\FormGenerator\Api\FormJsonSchemaExporter;
use Kavalhub\FormGenerator\Api\OpenApiDocumentBuilder;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Request\JsonElementRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;

final class FormApiRouter
{
    public function __construct(
        private readonly CatalogRepositoryInterface $repository,
        private readonly AddCategory $addCategory,
        private readonly AddFacet $addFacet,
        private readonly AddProduct $addProduct,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function dispatch(array $payload): FormApiResponse
    {
        $form = (string)($payload['form'] ?? '');
        $action = (string)($payload['action'] ?? 'submit');
        $values = is_array($payload['values'] ?? null) ? $payload['values'] : [];

        if ($form === 'seed' && $action === 'run') {
            return (new TestDataSeeder($this))->run();
        }

        $requestData = FormValueMapper::toRequestData($form, $action, $values);
        $request = JsonElementRequest::fromArray($requestData);
        $validator = new ElementValidator($request);
        $handler = new FormApiHandler($validator);

        return match ($form) {
            'category' => $this->handleCategory($handler, $validator, $action, $values, $payload),
            'facet' => $this->handleFacet($handler, $validator, $action, $values, $payload),
            'product' => $this->handleProduct($handler, $validator, $action, $values),
            'filter' => $this->handleFilter($handler, $validator, $action, $values),
            default => throw new InvalidArgumentException('Unknown form: ' . $form),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function openApiSpec(): array
    {
        $validator = new ElementValidator(new JsonElementRequest());

        $forms = [
            [
                'key' => 'category',
                'actions' => ['field', 'submit'],
                'form' => new AddCategoryForm($this->repository, $validator),
            ],
            [
                'key' => 'facet',
                'actions' => ['field', 'submit'],
                'form' => new AddFacetForm($this->repository, $validator),
            ],
            [
                'key' => 'product',
                'actions' => ['submit'],
                'form' => new AddProductForm($this->repository, $validator),
            ],
            [
                'key' => 'filter',
                'actions' => ['apply', 'refresh_categories', 'describe'],
                'form' => new FacetProductForm($this->repository, $validator),
                'valuesSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'categories' => ['type' => 'array', 'items' => ['type' => 'integer']],
                        'show_empty_categories' => ['type' => 'boolean'],
                        'facets' => [
                            'type' => 'object',
                            'additionalProperties' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'seed',
                'actions' => ['run'],
                'form' => new AddCategoryForm($this->repository, $validator),
                'valuesSchema' => ['type' => 'object'],
            ],
        ];

        return (new OpenApiDocumentBuilder('form-generator demo API', '/api.php', $forms))->build();
    }

    /**
     * @param array<string, mixed> $values
     * @param array<string, mixed> $payload
     */
    private function handleCategory(
        FormApiHandler $handler,
        ElementValidator $validator,
        string $action,
        array $values,
        array $payload,
    ): FormApiResponse {
        $form = new AddCategoryForm($this->repository, $validator);
        if ($action === 'field') {
            $target = $this->resolveTargetId($form, (string)($payload['target'] ?? 'name'));

            return $handler->handleField($form, $target);
        }
        if ($action !== 'submit') {
            throw new InvalidArgumentException('Unknown action: ' . $action);
        }

        $response = $handler->handleForm($form);
        if (!$response->isValid()) {
            return $response;
        }

        $category = $this->addCategory->execute(new Category(
            (string)($values['name'] ?? ''),
            isset($values['sort']) ? (int)$values['sort'] : null,
        ));

        return $response
            ->withResult([
                'id' => (int)$category->getUuid(),
                'name' => $category->getName(),
                'sort' => $category->getSort(),
            ])
            ->setMessage('Категория добавлена');
    }

    /**
     * @param array<string, mixed> $values
     * @param array<string, mixed> $payload
     */
    private function handleFacet(
        FormApiHandler $handler,
        ElementValidator $validator,
        string $action,
        array $values,
        array $payload,
    ): FormApiResponse {
        $form = new AddFacetForm($this->repository, $validator);
        if ($action === 'field') {
            $target = $this->resolveTargetId($form, (string)($payload['target'] ?? 'name'));

            return $handler->handleField($form, $target);
        }
        if ($action !== 'submit') {
            throw new InvalidArgumentException('Unknown action: ' . $action);
        }

        $response = $handler->handleForm($form);
        if (!$response->isValid()) {
            return $response;
        }

        $facet = $this->addFacet->execute(new Facet((string)($values['name'] ?? '')));

        return $response
            ->withResult([
                'id' => (int)$facet->getUuid(),
                'name' => $facet->getName(),
            ])
            ->setMessage('Фасет добавлен');
    }

    /**
     * @param array<string, mixed> $values
     */
    private function handleProduct(
        FormApiHandler $handler,
        ElementValidator $validator,
        string $action,
        array $values,
    ): FormApiResponse {
        if ($action !== 'submit') {
            throw new InvalidArgumentException('Unknown action: ' . $action);
        }

        $form = new AddProductForm($this->repository, $validator);
        $response = $handler->handleForm($form);
        if (!$response->isValid()) {
            return $response;
        }

        $product = $this->addProduct->execute($form->toProduct($this->repository));

        return $response
            ->withResult(['id' => (int)$product->getUuid()])
            ->setMessage('Товар добавлен');
    }

    /**
     * @param array<string, mixed> $values
     */
    private function handleFilter(
        FormApiHandler $handler,
        ElementValidator $validator,
        string $action,
        array $values,
    ): FormApiResponse {
        $form = new FacetProductForm($this->repository, $validator);

        if ($action === 'refresh_categories') {
            $form->refreshCategoryGroup();
            $list = new CategoryList($this->repository);
            if (empty($values['show_empty_categories'])) {
                $list->addRawFilter('tpc.category_id IS NOT NULL');
            }
            $categories = [];
            foreach ($list->__toArray() as $row) {
                $categories[] = [
                    'id' => (int)$row['id'],
                    'name' => (string)$row['name'],
                    'count' => (int)$row['count'],
                ];
            }

            return new FormApiResponse(
                valid: true,
                result: ['categories' => $categories],
            );
        }

        if ($action === 'describe') {
            if ($values !== []) {
                $form->applyFilter(true);
            }

            return new FormApiResponse(
                valid: true,
                result: ['values' => FormJsonSchemaExporter::export($form)],
            );
        }

        if ($action !== 'apply') {
            throw new InvalidArgumentException('Unknown action: ' . $action);
        }

        if (!$form->applyFilter(true)) {
            return new FormApiResponse(valid: false, message: 'Укажите категорию');
        }

        return new FormApiResponse(
            valid: true,
            data: [],
            result: FilterApiPresenter::present($form),
            message: 'Фильтр обновлён',
        );
    }

    private function resolveTargetId(ElementInterface $form, string $shortName): string
    {
        $field = $form->getByName($shortName);
        if ($field->getName() === '') {
            throw new InvalidArgumentException('Unknown field: ' . $shortName);
        }

        return $field->getId();
    }

    public function repository(): CatalogRepositoryInterface
    {
        return $this->repository;
    }
}
