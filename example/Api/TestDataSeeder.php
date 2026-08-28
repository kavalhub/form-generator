<?php
declare(strict_types=1);

namespace Kavalhub\Example\Api;

use Kavalhub\FormGenerator\Api\FormApiResponse;
use Throwable;

final class TestDataSeeder
{
    private const CATEGORIES = [
        ['name' => 'Электроника', 'sort' => 1],
        ['name' => 'Одежда', 'sort' => 2],
        ['name' => 'Книги', 'sort' => 3],
        ['name' => 'Бытовая техника', 'sort' => 4],
        ['name' => 'Спорт', 'sort' => 5],
        ['name' => 'Пустая категория', 'sort' => 6],
    ];

    private const EMPTY_CATEGORY_NAME = 'Пустая категория';

    private const FACETS = [
        'Цвет',
        'Размер',
        'Бренд',
        'Материал',
        'Объём',
    ];

    /**
     * Профили категорий: какие фасеты и значения допустимы для товаров.
     *
     * @var array<string, array{patterns: list<list<string>>, values: array<string, list<string>>}>
     */
    private const CATEGORY_PROFILES = [
        'Электроника' => [
            'patterns' => [
                ['Цвет', 'Бренд', 'Объём'],
                ['Цвет', 'Бренд', 'Объём', 'Материал'],
                ['Бренд', 'Объём'],
                ['Цвет', 'Бренд', 'Материал'],
            ],
            'values' => [
                'Цвет' => ['Чёрный', 'Белый', 'Серый', 'Синий'],
                'Бренд' => ['Samsung', 'Apple', 'Sony', 'Xiaomi', 'Huawei'],
                'Объём' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
                'Материал' => ['Пластик', 'Металл'],
            ],
        ],
        'Одежда' => [
            'patterns' => [
                ['Цвет', 'Размер', 'Бренд'],
                ['Цвет', 'Размер', 'Материал', 'Бренд'],
                ['Размер', 'Бренд'],
                ['Цвет', 'Материал', 'Бренд'],
            ],
            'values' => [
                'Цвет' => ['Чёрный', 'Белый', 'Серый', 'Синий', 'Красный', 'Зелёный'],
                'Размер' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'Бренд' => ['Nike', 'Adidas', 'Zara', 'H&M', 'Uniqlo'],
                'Материал' => ['Хлопок', 'Кожа', 'Полиэстер', 'Шерсть'],
            ],
        ],
        'Книги' => [
            'patterns' => [
                ['Бренд'],
            ],
            'values' => [
                'Бренд' => ['Эксмо', 'АСТ', 'Питер', 'Манн', 'Альпина'],
            ],
        ],
        'Бытовая техника' => [
            'patterns' => [
                ['Цвет', 'Бренд', 'Материал'],
                ['Бренд', 'Материал'],
                ['Цвет', 'Бренд'],
            ],
            'values' => [
                'Цвет' => ['Белый', 'Серый', 'Чёрный', 'Серебристый'],
                'Бренд' => ['Bosch', 'LG', 'Philips', 'Electrolux', 'Beko'],
                'Материал' => ['Пластик', 'Металл'],
            ],
        ],
        'Спорт' => [
            'patterns' => [
                ['Цвет', 'Размер', 'Бренд'],
                ['Цвет', 'Размер', 'Материал', 'Бренд'],
                ['Размер', 'Бренд'],
            ],
            'values' => [
                'Цвет' => ['Чёрный', 'Синий', 'Красный', 'Зелёный', 'Оранжевый'],
                'Размер' => ['S', 'M', 'L', 'XL'],
                'Бренд' => ['Nike', 'Adidas', 'Reebok', 'Puma', 'Decathlon'],
                'Материал' => ['Полиэстер', 'Неопрен', 'Резина'],
            ],
        ],
    ];

    public function __construct(
        private readonly FormApiRouter $router,
    ) {
    }

    public function run(): FormApiResponse
    {
        try {
            $storage = $this->router->repository();
            $storage->truncateDemoData();
            $storage->ensurePriceCurrency();

            foreach (self::CATEGORIES as $category) {
                $response = $this->router->dispatch([
                    'form' => 'category',
                    'action' => 'submit',
                    'values' => $category,
                ]);
                if (!$response->isValid()) {
                    return $response->setMessage('Ошибка при создании категории');
                }
            }

            foreach (self::FACETS as $facetName) {
                $response = $this->router->dispatch([
                    'form' => 'facet',
                    'action' => 'submit',
                    'values' => ['name' => $facetName],
                ]);
                if (!$response->isValid()) {
                    return $response->setMessage('Ошибка при создании фасета');
                }
            }

            $categoryIdsByName = array_filter(
                $storage->getCategoryIdsByName(),
                static fn (string $name): bool => $name !== self::EMPTY_CATEGORY_NAME,
                ARRAY_FILTER_USE_KEY,
            );
            $facetIds = $storage->getFacetIdsByName();

            $categoryNames = array_keys($categoryIdsByName);
            $productsPerCategory = (int) (200 / count($categoryNames));

            $created = 0;
            $productIndex = 1;
            foreach ($categoryNames as $categoryName) {
                $profile = self::CATEGORY_PROFILES[$categoryName] ?? ['patterns' => [['Бренд']], 'values' => ['Бренд' => ['Generic']]];
                for ($j = 0; $j < $productsPerCategory; $j++) {
                    $pattern = $profile['patterns'][$j % count($profile['patterns'])];
                    $facets = [];
                    foreach ($pattern as $facetIndex => $facetName) {
                        $pool = $profile['values'][$facetName];
                        $facets[(string)$facetIds[$facetName]] = $pool[($j + $facetIndex) % count($pool)];
                    }

                    $response = $this->router->dispatch([
                        'form' => 'product',
                        'action' => 'submit',
                        'values' => [
                            'name' => sprintf('%s #%03d', $categoryName, $j + 1),
                            'price' => round(500 + ($productIndex * 137.5) % 99500, 2),
                            'category' => $categoryIdsByName[$categoryName],
                            'facets' => $facets,
                        ],
                    ]);
                    if (!$response->isValid()) {
                        return $response->setMessage('Ошибка при создании товара #' . $productIndex);
                    }
                    $created++;
                    $productIndex++;
                }
            }

            return new FormApiResponse(
                valid: true,
                result: [
                    'categories' => count(self::CATEGORIES),
                    'facets' => count(self::FACETS),
                    'products' => $created,
                ],
                message: 'Тестовые данные добавлены',
            );
        } catch (Throwable $exception) {
            return new FormApiResponse(
                valid: false,
                message: 'Ошибка наполнения: ' . $exception->getMessage(),
            );
        }
    }
}
