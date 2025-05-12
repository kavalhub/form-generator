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

    public function addCategory(Category $category): bool
    {
        $query = $this->pdo->prepare('INSERT IGNORE INTO temp_category (name) VALUES (:name)');
        $query->bindValue(':name', $category->getName());

        return $query->execute();
    }
}
