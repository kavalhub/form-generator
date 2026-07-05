<?php
declare(strict_types=1);

namespace Kavalhub\Tests\FormGenerator\Example;

use Kavalhub\Example\Domain\Category;
use Kavalhub\Example\Env\AddCategoryForm;
use Kavalhub\Example\Env\AddProductForm;
use Kavalhub\Example\Env\Storage;
use Kavalhub\Example\UseCase\AddProduct;
use Kavalhub\FormGenerator\Request\ArrayRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;
use PDO;
use PHPUnit\Framework\TestCase;

final class ExampleFormsTest extends TestCase
{
    private function createMemoryStorage(): Storage
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO sqlite driver is required for this test.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE temp_category (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT UNIQUE, sort INTEGER DEFAULT 0)');
        $pdo->exec('CREATE TABLE temp_product (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)');
        $pdo->exec('CREATE TABLE temp_product_category (id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, product_id INTEGER)');
        $pdo->exec('CREATE TABLE temp_facet (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, element TEXT DEFAULT \'InputText\')');
        $pdo->exec('CREATE TABLE temp_product_facet (id INTEGER PRIMARY KEY AUTOINCREMENT, facet_id INTEGER, product_id INTEGER, value TEXT)');
        $pdo->exec('CREATE TABLE temp_price (id INTEGER PRIMARY KEY, currency TEXT)');
        $pdo->exec('CREATE TABLE temp_product_price (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER, price_id INTEGER, value REAL)');
        $pdo->exec('INSERT INTO temp_price (id, currency) VALUES (1, \'RUB\')');
        $pdo->exec('INSERT INTO temp_facet (name, element) VALUES (\'Цвет\', \'InputText\')');

        return new Storage($pdo);
    }

    public function testAddCategoryFormValidatesWithPostRequest(): void
    {
        $storage = $this->createMemoryStorage();
        $form = new AddCategoryForm($storage, new ElementValidator(new ArrayRequest([
            'addCategory_name' => 'Новая',
            'addCategory_sort' => '1',
            'addCategory_submit' => 'Добавить',
        ])));

        $this->assertTrue($form->validate());
    }

    public function testAddProductFormContainsCategoryAndFacetFields(): void
    {
        $storage = $this->createMemoryStorage();
        $storage->addCategory(new Category('Электроника', 1));
        $form = new AddProductForm($storage, new ElementValidator(new ArrayRequest([])));
        $html = $form->render();

        $this->assertStringContainsString('name="addProduct_name"', $html);
        $this->assertStringContainsString('name="addProduct_category"', $html);
        $this->assertStringContainsString('name="addProduct_facet_1"', $html);
        $this->assertStringContainsString('<select', $html);
    }

    public function testAddProductFormCollectsFacetValuesAfterValidation(): void
    {
        $storage = $this->createMemoryStorage();
        $storage->addCategory(new Category('Электроника', 1));
        $form = new AddProductForm($storage, new ElementValidator(new ArrayRequest([
            'addProduct_name' => 'Товар',
            'addProduct_price' => '100',
            'addProduct_category' => '1',
            'addProduct_facet_1' => 'Красный',
            'addProduct_submit' => 'Добавить',
        ])));

        $this->assertTrue($form->validate());
        $this->assertSame([1 => 'Красный'], $form->getFacetValues());
    }

    public function testAddProductFormBuildsDomainProductAndPersistsIt(): void
    {
        $storage = $this->createMemoryStorage();
        $storage->addCategory(new Category('Электроника', 1));
        $form = new AddProductForm($storage, new ElementValidator(new ArrayRequest([
            'addProduct_name' => 'Товар',
            'addProduct_price' => '100',
            'addProduct_category' => '1',
            'addProduct_facet_1' => 'Красный',
            'addProduct_submit' => 'Добавить',
        ])));

        $this->assertTrue($form->validate());

        $product = $form->toProduct($storage);
        $this->assertSame('Товар', $product->getName());
        $this->assertSame(100.0, $product->getPrice());
        $this->assertSame('Электроника', $product->getCategory()->getName());
        $this->assertNull($product->getUuid());

        $saved = (new AddProduct($storage))->execute($product);
        $this->assertSame('1', $saved->getUuid());
        $this->assertSame('Товар', $saved->getName());

        $pdo = new \ReflectionProperty(Storage::class, 'pdo');
        $pdo->setAccessible(true);
        $db = $pdo->getValue($storage);
        $this->assertSame(1, (int)$db->query('SELECT COUNT(*) FROM temp_product_facet')->fetchColumn());
    }
}
