# kavalhub/form-generator-twig

Twig-декоратор и шаблоны `{ClassName}.html.twig` для [kavalhub/form-generator](https://github.com/kavalhub/form-generator).

## Установка

```bash
composer require kavalhub/form-generator-twig
```

## Использование

```php
use Kavalhub\FormGenerator\Twig\TwigDecorator;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;

$form = (new Form('contact'))
    ->addElement((new InputText('email'))->setRequired());

echo (new TwigDecorator($form))->getHtml();
```

## Кастомные шаблоны

```php
echo (new TwigDecorator($input))
    ->setTemplate(__DIR__ . '/../resources/Twig')
    ->getHtml();

$input->setPath('CustomElements/InputText.html.twig');
```

В шаблоне: `element`, `decorator`, `decorator.decorateChild(child).html`.

## AJAX

```php
use Kavalhub\FormGenerator\Twig\TwigAjaxRenderStrategy;

$handler = new ElementAjaxHandler(
    new ElementValidator($request),
    new TwigAjaxRenderStrategy('/path/to/custom/templates'),
);
```

## Лицензия

MIT
