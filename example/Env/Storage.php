<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\Example\Domain\Category;
use Generator;
use PDO;

readonly class Storage
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getCategoryList(): Generator
    {
        $query = $this->pdo->query('SELECT * FROM temp_category');
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
