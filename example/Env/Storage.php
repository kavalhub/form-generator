<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\Domain\Category;
use Generator;
use PDO;
use PDOException;

readonly class Storage
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = 'mysql:host=172.19.0.1;port=3325;dbname=dks_slim;charset=UTF8';
        $username = 'wwwadmin';
        $password = 'gfhjkm';
        try {
            $this->pdo = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            exit ("Ошибка подключения к MySQL: " . $e->getMessage());
        }
    }

    public function getCategoryList(string $where = ''): Generator
    {
        $query = $this->pdo->query(
            'SELECT tc.id AS id, 
                            tc.name AS name, 
                            tc.sort AS sort,
                            COUNT(tpc.id) AS count
                        FROM temp_category AS tc
                                 LEFT JOIN temp_product_category AS tpc ON tpc.category_id = tc.id
                        ' . $where . '
                        GROUP BY tc.id, sort
                        ORDER BY sort;'
        );
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function getFacetList(string $where = ''): Generator
    {
        $query = $this->pdo->query(
            'SELECT tf.id AS id, 
                            tf.name AS name, 
                            COUNT(tpf.id) AS count
                        FROM temp_facet AS tf
                                 LEFT JOIN temp_product_facet AS tpf ON tpf.facet_id = tf.id
                        ' . $where . '                                             
                        GROUP BY tf.id
                        ORDER BY tf.name;'
        );
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function getProductList(string $where = ''): Generator
    {
        $query = $this->pdo->query(
            'SELECT tp.id as id,
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
                                 LEFT JOIN temp_price AS tpr ON tpr.id = tpp.price_id ' . $where
        );
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function addCategory(Category $category): Category
    {
        if ($cat = $this->getCategoryByName($category->getName())) {
            return $cat;
        }
        $query = $this->pdo->prepare('INSERT IGNORE INTO temp_category (name) VALUES (:name)');
        $query->bindValue(':name', $category->getName());
        $query->execute();

        return new Category($category->getName(), (string)$this->pdo->lastInsertId());
    }

    public function getCategoryByName(string $name): ?Category
    {
        $query = $this->pdo->prepare('SELECT * FROM temp_category WHERE name = :name');
        $query->bindValue(':name', $name);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return new Category((string)$row['name'], (string)$row['id']);
        }

        return null;
    }
}
