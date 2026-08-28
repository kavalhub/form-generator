<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Example;

use Kavalhub\Example\Application\UseCase\ProductList;
use Kavalhub\Example\Infrastructure\Persistence\PdoCatalogRepository;
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

        $list = new ProductList(new PdoCatalogRepository($pdo));
        $list->addCategoryIdsFilter(['1', '2', 'abc']);

        iterator_to_array($list->get());
    }

    public function testAddProductIdsFilterIgnoresEmptyList(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->never())->method('prepare');

        $list = new ProductList(new PdoCatalogRepository($pdo));
        $list->addProductIdsFilter([]);

        $this->assertTrue(true);
    }

    public function testAddPriceRangeFilterAddsBetweenCondition(): void
    {
        $pdo = $this->createMock(PDO::class);
        $statement = $this->createMock(PDOStatement::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->logicalAnd(
                $this->stringContains('tpp.value >= ?'),
                $this->stringContains('tpp.value <= ?'),
            ))
            ->willReturn($statement);
        $statement->expects($this->once())
            ->method('execute')
            ->with([100.0, 500.0]);

        $statement->method('fetch')->willReturn(false);

        $list = new ProductList(new PdoCatalogRepository($pdo));
        $list->addPriceRangeFilter(100.0, 500.0);

        iterator_to_array($list->get());
    }

    public function testToPageArrayReturnsSlice(): void
    {
        $pdo = $this->createMock(PDO::class);
        $statement = $this->createMock(PDOStatement::class);
        $pdo->method('prepare')->willReturn($statement);
        $statement->method('execute');
        $statement->method('fetch')->willReturnOnConsecutiveCalls(
            [
                'id' => 1,
                'facet_name' => 'Бренд',
                'facet_value' => 'A',
                'name' => 'One',
                'category' => 'Cat',
                'price' => '10',
                'currency' => 'RUB',
                'element' => 'InputCheckbox',
            ],
            [
                'id' => 2,
                'facet_name' => 'Бренд',
                'facet_value' => 'B',
                'name' => 'Two',
                'category' => 'Cat',
                'price' => '20',
                'currency' => 'RUB',
                'element' => 'InputCheckbox',
            ],
            [
                'id' => 3,
                'facet_name' => 'Бренд',
                'facet_value' => 'C',
                'name' => 'Three',
                'category' => 'Cat',
                'price' => '30',
                'currency' => 'RUB',
                'element' => 'InputCheckbox',
            ],
            false,
        );

        $list = new ProductList(new PdoCatalogRepository($pdo));
        $page = $list->toPageArray(2, 1);

        $this->assertCount(2, $page);
        $this->assertArrayHasKey(2, $page);
        $this->assertArrayHasKey(3, $page);
        $this->assertSame('Two', $page[2]['name']);
    }
}
