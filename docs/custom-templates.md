# Кастомные шаблоны декоратора

Декораторы рендерят элементы через PHP-шаблоны. В core остаются [`DecoratorInterface`](../src/Decorator/Interface/DecoratorInterface.php) и [`AbstractDecorator`](../src/Decorator/AbstractDecorator.php); готовая Bootstrap-тема — в пакете `kavalhub/form-generator-bootstrap`.

## Порядок поиска шаблона

Для элемента `InputText` декоратор ищет файлы в таком порядке:

1. `{element->getPath()}/InputText.php` — точечная подмена на элементе (`$input->setPath('/path')`)
2. `{customPath}/InputText.php` — каталог из `setTemplate()` (переопределения проекта)
3. `{decoratorDefaultPath}/InputText.php` — шаблоны пакета темы
4. То же для родительского класса элемента (например `HtmlElementWithValue.php`)
5. `$element->render()` — fallback без темы

## Переопределение в приложении

1. Установите bootstrap-пакет:

```bash
composer require kavalhub/form-generator-bootstrap
```

2. Скопируйте нужный шаблон из `vendor/kavalhub/form-generator-bootstrap/templates/`, например `InputText.php`, в каталог проекта `resources/form-templates/`.

3. Подключите каталог:

```php
use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator;

$html = (new BootstrapDecorator($input))
    ->setTemplate(__DIR__ . '/../resources/form-templates')
    ->getHtml();
```

В demo-проекте обёртка [`FormRenderer`](../../example/Env/FormRenderer.php) подключает `resources/form-templates/` автоматически, если в каталоге есть `.php`-файлы.

## Содержимое шаблона

Шаблон — обычный PHP-файл, подключаемый через `include`. В области видимости доступен `$this` — экземпляр декоратора; элемент — в `$this->element` (клон с классами валидации).

Пример из bootstrap (`InputText.php`):

```php
<?php
$this->element->addClass(['form-control']);
$error = !empty($this->element->getError())
    ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors() . '</div>'
    : '';
return '<div class="form-group">' . $this->element->render() . $error . '</div>';
```

Для контейнеров (`Form`, `Group`) дочерние элементы рендерятся рекурсивно: `(new $this($childElement))->getHtml()`.

## Своя тема (не Bootstrap)

1. Создайте класс, наследующий `AbstractDecorator`.
2. Задайте `$path`, `$errorClass`, `$successClass`.
3. Положите шаблоны в каталог и при необходимости переопределите `getHtml()`.
4. Для AJAX реализуйте [`AjaxRenderStrategyInterface`](../src/Ajax/Interface/AjaxRenderStrategyInterface.php) или используйте [`NullAjaxRenderStrategy`](../src/Ajax/NullAjaxRenderStrategy.php) без CSS-классов темы.

## Миграция с 3.2

| Было | Стало |
|------|-------|
| `Kavalhub\FormGenerator\Decorator\Bootstrap\BootstrapDecorator` | `Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator` + `composer require kavalhub/form-generator-bootstrap` |
| `new ElementAjaxHandler($validator)` | `new ElementAjaxHandler($validator, $renderStrategy)` |

Старый namespace в 3.3 помечен deprecated и работает при установленном bootstrap-пакете.
