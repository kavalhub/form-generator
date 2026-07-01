<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Generator;
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
            $conditions[] = '(f.name = ? AND pf.value IN (' . implode(',', $valuePlaceholders) . '))';
            $params[] = $name;
            foreach ($values as $value) {
                $params[] = $value;
            }
        }
        $sql = 'SELECT pf.product_id
                    FROM temp_product_facet pf
                        JOIN temp_facet f ON pf.facet_id = f.id
                    WHERE ' . implode(' OR ', $conditions) . '
                    GROUP BY pf.product_id
                    HAVING COUNT(DISTINCT f.name) = ?';
        $params[] = count($facetList);
        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN));
    }

    public function addCategory(\Kavalhub\Example\Domain\Category $category): \Kavalhub\Example\Domain\Category
    {
        if ($cat = $this->getCategoryByName($category->getName())) {
            return $cat;
        }
        $query = $this->pdo->prepare('INSERT IGNORE INTO temp_category (name) VALUES (:name)');
        $query->bindValue(':name', $category->getName());
        $query->execute();

        return new \Kavalhub\Example\Domain\Category($category->getName(), (string)$this->pdo->lastInsertId());
    }

    public function getCategoryByName(string $name): ?\Kavalhub\Example\Domain\Category
    {
        $query = $this->pdo->prepare('SELECT * FROM temp_category WHERE name = :name');
        $query->bindValue(':name', $name);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return new \Kavalhub\Example\Domain\Category((string)$row['name'], (string)$row['id']);
        }

        return null;
    }
}
