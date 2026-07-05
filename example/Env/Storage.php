<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Generator;
use InvalidArgumentException;
use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Domain\Facet;
use Kavalhub\Example\Domain\Product;
use Kavalhub\Example\Domain\Product\ProductFacet;
use PDO;
use PDOException;

readonly class Storage
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo;

            return;
        }
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'dks_slim';
        $username = getenv('DB_USER') ?: 'wwwadmin';
        $password = getenv('DB_PASSWORD') ?: '';
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=UTF8', $host, $port, $dbname);
        try {
            $this->pdo = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            throw new PDOException('Ошибка подключения к MySQL: ' . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function getCategoryList(array $where = [], array $params = []): Generator
    {
        $sql = 'SELECT tc.id AS id,
                            tc.name AS name,
                            tc.sort AS sort,
                            COUNT(tpc.id) AS count
                        FROM temp_category AS tc
                                 LEFT JOIN temp_product_category AS tpc ON tpc.category_id = tc.id';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY tc.id, sort ORDER BY sort';
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function getFacetList(array $where = [], array $params = []): Generator
    {
        $sql = 'SELECT tf.id AS id,
                            tf.name AS name,
                            COUNT(tpf.id) AS count
                        FROM temp_facet AS tf
                                 LEFT JOIN temp_product_facet AS tpf ON tpf.facet_id = tf.id';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY tf.id ORDER BY tf.name';
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function getProductList(array $where = [], array $params = []): Generator
    {
        $sql = 'SELECT tp.id as id,
                            tc.name AS category,
                            tf.element AS element,
                            tf.name AS facet_name,
                            tpf.value AS facet_value,
                            tp.name AS name,
                            tpp.value AS price,
                            tpr.currency AS currency
                        FROM temp_product AS tp
                                 LEFT JOIN temp_product_category AS tpc ON tpc.product_id = tp.id
                                 LEFT JOIN temp_category AS tc ON tc.id = tpc.category_id
                                 JOIN temp_product_facet AS tpf ON tpf.product_id = tp.id
                                 LEFT JOIN temp_facet as tf ON tf.id = tpf.facet_id
                                 LEFT JOIN temp_product_price AS tpp ON tpp.product_id = tp.id
                                 LEFT JOIN temp_price AS tpr ON tpr.id = tpp.price_id';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    /**
     * @param array<string, string[]> $facetList
     * @return int[]
     */
    public function getProductIdsByFacets(array $facetList): array
    {
        $facetList = array_filter(
            $facetList,
            static fn (mixed $values): bool => is_array($values) && $values !== [],
        );
        if ($facetList === []) {
            return [];
        }
        $conditions = [];
        $params = [];
        foreach ($facetList as $name => $values) {
            $valuePlaceholders = [];
            foreach ($values as $value) {
                $valuePlaceholders[] = '?';
            }
            if ($valuePlaceholders === []) {
                continue;
            }
            $conditions[] = '(f.name = ? AND pf.value IN (' . implode(',', $valuePlaceholders) . '))';
            $params[] = $name;
            foreach ($values as $value) {
                $params[] = $value;
            }
        }
        if ($conditions === []) {
            return [];
        }
        $sql = 'SELECT pf.product_id
                    FROM temp_product_facet pf
                        JOIN temp_facet f ON pf.facet_id = f.id
                    WHERE ' . implode(' OR ', $conditions) . '
                    GROUP BY pf.product_id
                    HAVING COUNT(DISTINCT f.name) = ?';
        $params[] = count($conditions);
        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN));
    }

    public function addCategory(Category $category): Category
    {
        if ($cat = $this->getCategoryByName($category->getName())) {
            return $cat;
        }
        $query = $this->pdo->prepare('INSERT INTO temp_category (name, sort) VALUES (:name, :sort)');
        $query->bindValue(':name', $category->getName());
        $query->bindValue(':sort', $category->getSort() ?? 0, PDO::PARAM_INT);
        $query->execute();

        return new Category(
            $category->getName(),
            $category->getSort(),
            (string)$this->pdo->lastInsertId(),
        );
    }

    public function addFacet(Facet $facet): Facet
    {
        if ($existing = $this->getFacetByName($facet->getName())) {
            return $existing;
        }
        $query = $this->pdo->prepare('INSERT INTO temp_facet (name, element) VALUES (:name, :element)');
        $query->bindValue(':name', $facet->getName());
        $query->bindValue(':element', $facet->getElement());
        $query->execute();

        return new Facet(
            $facet->getName(),
            (string)$this->pdo->lastInsertId(),
            $facet->getElement(),
        );
    }

    public function getCategoryById(int $id): ?Category
    {
        $query = $this->pdo->prepare('SELECT * FROM temp_category WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return new Category((string)$row['name'], (int)$row['sort'], (string)$row['id']);
        }

        return null;
    }

    public function getFacetById(int $id): ?Facet
    {
        $query = $this->pdo->prepare('SELECT * FROM temp_facet WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return new Facet(
                (string)$row['name'],
                (string)$row['id'],
                (string)$row['element'],
            );
        }

        return null;
    }

    private function getFacetByName(string $name): ?Facet
    {
        $query = $this->pdo->prepare('SELECT * FROM temp_facet WHERE name = :name');
        $query->bindValue(':name', $name);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return new Facet(
                (string)$row['name'],
                (string)$row['id'],
                (string)$row['element'],
            );
        }

        return null;
    }

    public function addProduct(Product $product): Product
    {
        $categoryId = (int)$product->getCategory()->getUuid();
        if ($categoryId <= 0) {
            throw new InvalidArgumentException('Product category id is required');
        }

        $this->pdo->beginTransaction();
        try {
            $query = $this->pdo->prepare('INSERT INTO temp_product (name) VALUES (:name)');
            $query->bindValue(':name', $product->getName());
            $query->execute();
            $productId = (int)$this->pdo->lastInsertId();

            $query = $this->pdo->prepare(
                'INSERT INTO temp_product_category (category_id, product_id) VALUES (:category_id, :product_id)'
            );
            $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $query->execute();

            $query = $this->pdo->prepare(
                'INSERT INTO temp_product_price (product_id, price_id, value) VALUES (:product_id, 1, :value)'
            );
            $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $query->bindValue(':value', $product->getPrice());
            $query->execute();

            $facetQuery = $this->pdo->prepare(
                'INSERT INTO temp_product_facet (facet_id, product_id, value) VALUES (:facet_id, :product_id, :value)'
            );
            foreach ($product->getFacets() as $productFacet) {
                if (!$productFacet instanceof ProductFacet) {
                    continue;
                }
                $value = trim($productFacet->getValue());
                if ($value === '') {
                    continue;
                }
                $facet = $this->getFacetByName($productFacet->getName());
                if ($facet === null) {
                    continue;
                }
                $facetQuery->bindValue(':facet_id', (int)$facet->getUuid(), PDO::PARAM_INT);
                $facetQuery->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $facetQuery->bindValue(':value', $value);
                $facetQuery->execute();
            }

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return new Product(
            $product->getName(),
            $product->getPrice(),
            $product->getCategory(),
            $product->getFacets(),
            (string)$productId,
        );
    }

    private function getCategoryByName(string $name): ?Category
    {
        $query = $this->pdo->prepare('SELECT * FROM temp_category WHERE name = :name');
        $query->bindValue(':name', $name);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return new Category((string)$row['name'], (int)$row['sort'], (string)$row['id']);
        }

        return null;
    }

    public function deleteCategory(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM temp_product_category WHERE category_id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->pdo->prepare('DELETE FROM temp_category WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteFacet(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM temp_product_facet WHERE facet_id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->pdo->prepare('DELETE FROM temp_facet WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteProduct(int $id): void
    {
        $tables = [
            'DELETE FROM temp_product_facet WHERE product_id = :id',
            'DELETE FROM temp_product_price WHERE product_id = :id',
            'DELETE FROM temp_product_category WHERE product_id = :id',
            'DELETE FROM temp_product WHERE id = :id',
        ];
        foreach ($tables as $sql) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function truncateDemoData(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ([
            'temp_product_facet',
            'temp_product_price',
            'temp_product_category',
            'temp_product',
            'temp_facet',
            'temp_category',
        ] as $table) {
            $this->pdo->exec('TRUNCATE TABLE ' . $table);
        }
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function ensurePriceCurrency(): void
    {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO temp_price (id, currency) VALUES (1, :currency)');
        $stmt->bindValue(':currency', 'RUB');
        $stmt->execute();
    }

    /**
     * @return array<string, int> facet name => id
     */
    public function getFacetIdsByName(): array
    {
        $map = [];
        foreach ($this->getFacetList() as $row) {
            $map[(string)$row['name']] = (int)$row['id'];
        }

        return $map;
    }

    /**
     * @return array<string, int> category name => id
     */
    public function getCategoryIdsByName(): array
    {
        $map = [];
        foreach ($this->getCategoryList() as $row) {
            $map[(string)$row['name']] = (int)$row['id'];
        }

        return $map;
    }
}
