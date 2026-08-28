<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Example;

use Kavalhub\Example\Infrastructure\Persistence\PdoCatalogRepository;
use Kavalhub\Example\Presentation\Web\Form\FacetSelectionListener;
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

        $repository = new PdoCatalogRepository($pdo);
        $listener = new FacetSelectionListener($repository);

        $group = new Group('gcolor');
        $group->addElement((new InputCheckbox('color', 'red'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($group));

        $this->assertSame([10, 20], $listener->getProductIds());
        $this->assertSame(['color' => ['red']], $listener->getFacetList());
        $this->assertTrue($listener->hasSelection());
    }

    public function testResetClearsFacetSelection(): void
    {
        $repository = new PdoCatalogRepository($this->createMock(PDO::class));
        $listener = new FacetSelectionListener($repository);

        $group = new Group('gcolor');
        $group->addElement((new InputCheckbox('color', 'red'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($group));
        $listener->reset();

        $this->assertFalse($listener->hasSelection());
        $this->assertSame([], $listener->getFacetList());
    }

    public function testIgnoresCategoryGroupAndFormEvents(): void
    {
        $repository = new PdoCatalogRepository($this->createMock(PDO::class));
        $listener = new FacetSelectionListener($repository);

        $categoryGroup = new Group('gc');
        $categoryGroup->addElement((new InputCheckbox('cat', '1'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($categoryGroup));

        $form = new Group('fl');
        $form->addElement((new InputCheckbox('cat', '1'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($form));

        $this->assertFalse($listener->hasSelection());
        $this->assertSame([], $listener->getFacetList());
    }

    public function testAcceptsFacetGroupEvent(): void
    {
        $repository = new PdoCatalogRepository($this->createMock(PDO::class));
        $listener = new FacetSelectionListener($repository);

        $group = new Group('gБренд');
        $group->addElement((new InputCheckbox('Бренд', 'Apple'))->setChecked());
        $listener->onElementChanged(new ElementChangedEvent($group));

        $this->assertTrue($listener->hasSelection());
        $this->assertSame(['Бренд' => ['Apple']], $listener->getFacetList());
    }
}
