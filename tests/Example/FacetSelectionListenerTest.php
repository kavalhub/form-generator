<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Example;

use Kavalhub\Example\Env\FacetSelectionListener;
use Kavalhub\Example\Env\Storage;
use Kavalhub\FormGenerator\Event\ElementChangedEvent;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputCheckbox;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class FacetSelectionListenerTest extends TestCase
{
    public function testGetProductIdsDelegatesToStorage(): void
    {
        $pdo = $this->createMock(PDO::class);
        $statement = $this->createMock(PDOStatement::class);
        $pdo->method('prepare')->willReturn($statement);
        $statement->method('execute');
        $statement->method('fetchAll')->willReturn(['10', '20']);

        $storage = new Storage($pdo);
        $listener = new FacetSelectionListener($storage);

        $group = new Group('facets');
        $group->addElement((new InputCheckbox('color', 'red'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($group));

        $this->assertSame([10, 20], $listener->getProductIds());
        $this->assertSame(['color' => ['red']], $listener->getFacetList());
        $this->assertTrue($listener->hasSelection());
    }

    public function testResetClearsFacetSelection(): void
    {
        $storage = new Storage($this->createMock(PDO::class));
        $listener = new FacetSelectionListener($storage);

        $group = new Group('facets');
        $group->addElement((new InputCheckbox('color', 'red'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($group));
        $listener->reset();

        $this->assertFalse($listener->hasSelection());
        $this->assertSame([], $listener->getFacetList());
    }
}
