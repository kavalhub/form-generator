<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Example;

use Kavalhub\Example\Env\ObserverFacet;
use Kavalhub\Example\Env\Storage;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class ObserverFacetTest extends TestCase
{
    public function testGetProductIdsDelegatesToStorage(): void
    {
        $pdo = $this->createMock(PDO::class);
        $statement = $this->createMock(PDOStatement::class);
        $pdo->method('prepare')->willReturn($statement);
        $statement->method('execute');
        $statement->method('fetchAll')->willReturn(['10', '20']);

        $storage = new Storage($pdo);
        $observer = new ObserverFacet($storage);

        $group = new Group('facets');
        $group->addElement((new InputCheckbox('color', 'red'))->setChecked());
        $observer->update($group);

        $this->assertSame([10, 20], $observer->getProductIds());
        $this->assertSame(['color' => ['red']], $observer->getFacetList());
        $this->assertTrue($observer->hasSelection());
    }

    public function testResetClearsFacetSelection(): void
    {
        $storage = new Storage($this->createMock(PDO::class));
        $observer = new ObserverFacet($storage);

        $group = new Group('facets');
        $group->addElement((new InputCheckbox('color', 'red'))->setChecked());
        $observer->update($group);
        $observer->reset();

        $this->assertFalse($observer->hasSelection());
        $this->assertSame([], $observer->getFacetList());
    }
}
