# kavalhub/form-generator-bootstrap

Bootstrap-декоратор и PHP-шаблоны для [kavalhub/form-generator](https://github.com/kavalhub/form-generator).

## Установка

```bash
composer require kavalhub/form-generator-bootstrap
```

## Использование

```php
use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;

$form = (new Form('contact'))
    ->addElement((new InputText('email'))->setRequired());

echo (new BootstrapDecorator($form))->getHtml();
```

## AJAX

Передайте `BootstrapAjaxRenderStrategy` в `ElementAjaxHandler`:

```php
use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Validator\ElementValidator;

$handler = new ElementAjaxHandler(
    new ElementValidator($request),
    new BootstrapAjaxRenderStrategy(),
);
```

Опционально укажите каталог с кастомными шаблонами (см. [docs/custom-templates.md](../../docs/custom-templates.md) в основном репозитории).

## Свой декоратор

Наследуйте [`AbstractDecorator`](../form-generator/src/Decorator/AbstractDecorator.php) из core и положите шаблоны рядом с классом или укажите путь через `setTemplate()`.

## Лицензия

MIT
