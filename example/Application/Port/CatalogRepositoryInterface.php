<?php
declare(strict_types=1);

namespace Kavalhub\Example\Application\Port;

use Generator;
use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Domain\Facet;
use Kavalhub\Example\Domain\Product;

interface CatalogRepositoryInterface
{
    /**
     * @param list<string> $where
     * @param list<scalar> $params
     */
    public function getCategoryList(array $where = [], array $params = []): Generator;

    /**
     * @param list<string> $where
     * @param list<scalar> $params
     */
    public function getFacetList(array $where = [], array $params = []): Generator;

    /**
     * @param list<string> $where
     * @param list<scalar> $params
     */
    public function getProductList(array $where = [], array $params = []): Generator;

    /**
     * @param list<string> $where
     * @param list<scalar> $params
     * @return array{min: float, max: float, currency: string}
     */
    public function getPriceBounds(array $where = [], array $params = []): array;

    /**
     * @param array<string, string[]> $facetList
     * @return int[]
     */
    public function getProductIdsByFacets(array $facetList): array;

    public function addCategory(Category $category): Category;

    public function addFacet(Facet $facet): Facet;

    public function getCategoryById(int $id): ?Category;

    public function getFacetById(int $id): ?Facet;

    public function addProduct(Product $product): Product;

    public function deleteCategory(int $id): void;

    public function deleteFacet(int $id): void;

    public function deleteProduct(int $id): void;

    public function truncateDemoData(): void;

    public function ensurePriceCurrency(): void;

    /**
     * @return array<string, int>
     */
    public function getFacetIdsByName(): array;

    /**
     * @return array<string, int>
     */
    public function getCategoryIdsByName(): array;
}
