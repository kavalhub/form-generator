# kavalhub/form-generator-bootstrap

Bootstrap-тема и PHP-шаблоны для [kavalhub/form-generator](https://github.com/kavalhub/form-generator).

## Установка

```bash
composer require kavalhub/form-generator-bootstrap
```

Требует `kavalhub/form-generator` **^4.0**.

## Использование

```php
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Render\RenderTheme;

$form = (new Form('contact'))
    ->addElement((new InputText('email'))->setRequired());

$renderer = (new ElementRenderer())->setTheme(RenderTheme::bootstrap());
echo $renderer->html($form);
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

## Кастомные шаблоны

Передайте `customPath` в `BootstrapTheme::create()` или `RenderTheme::bootstrap(null, $customPath)`.

## Лицензия

MIT
