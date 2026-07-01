<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Example;

use Kavalhub\Example\UseCase\ProductList;
use Kavalhub\Example\Env\Storage;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class ProductListTest extends TestCase
{
    public function testAddCategoryIdsFilterCastsToIntegers(): void
    {
        $pdo = $this->createMock(PDO::class);
        $statement = $this->createMock(PDOStatement::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('tpc.category_id IN (?,?)'))
            ->willReturn($statement);
        $statement->expects($this->once())
            ->method('execute')
            ->with([1, 2]);

        $statement->method('fetch')->willReturn(false);

        $list = new ProductList(new Storage($pdo));
        $list->addCategoryIdsFilter(['1', '2', 'abc']);

        iterator_to_array($list->get());
    }

    public function testAddProductIdsFilterIgnoresEmptyList(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->never())->method('prepare');

        $list = new ProductList(new Storage($pdo));
        $list->addProductIdsFilter([]);

        $this->assertTrue(true);
    }
}
