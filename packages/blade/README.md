# kavalhub/form-generator-blade

Blade-декоратор и шаблоны `{ClassName}.php` для [kavalhub/form-generator](https://github.com/kavalhub/form-generator).

## Установка

```bash
composer require kavalhub/form-generator-blade
```

## Использование

```php
use Kavalhub\FormGenerator\Blade\BladeDecorator;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;

$form = (new Form('contact'))
    ->addElement((new InputText('email'))->setRequired());

echo (new BladeDecorator($form))->render();
```

## Кастомные шаблоны

```php
echo (new BladeDecorator($input))
    ->setTemplate(__DIR__ . '/../resources/form-templates')
    ->render();
```

В шаблоне доступны переменные `$element` и `$decorator` (для рекурсивного рендера дочерних элементов).

## AJAX

```php
use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Blade\BladeAjaxRenderStrategy;

$handler = new ElementAjaxHandler(
    new ElementValidator($request),
    new BladeAjaxRenderStrategy('/path/to/custom/templates'),
);
```

## Лицензия

MIT
