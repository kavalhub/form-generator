# kavalhub/form-generator-tailwind

Tailwind CSS декоратор и PHP-шаблоны для [kavalhub/form-generator](https://github.com/kavalhub/form-generator).

## Установка

```bash
composer require kavalhub/form-generator-tailwind
```

Подключите Tailwind CSS в приложении (CDN, сборка и т.д.).

## Использование

```php
use Kavalhub\FormGenerator\Tailwind\TailwindDecorator;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;

$form = (new Form('contact'))
    ->addElement((new InputText('email'))->setRequired());

echo (new TailwindDecorator($form))->getHtml();
```

## AJAX

```php
use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Tailwind\TailwindAjaxRenderStrategy;

$handler = new ElementAjaxHandler(
    new ElementValidator($request),
    new TailwindAjaxRenderStrategy('/path/to/custom/templates'),
);
```

## Лицензия

MIT
