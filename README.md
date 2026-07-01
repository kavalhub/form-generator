# form-generator

PHP-библиотека для программного создания HTML-форм, привязки данных из запроса, валидации и рендеринга с опциональным Bootstrap-декоратором.

## Требования

- PHP ^8.2
- Composer

## Установка

```bash
composer require kavalhub/form-generator
```

## Быстрый старт

```php
use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Request\ElementRequest;
use Kavalhub\FormGenerator\Validator\ElementValidator;

$form = (new Form('contact'))
    ->addElement(
        (new InputText('email'))->setRequired()->setPlaceholder('Email')
    )
    ->addElement(
        (new InputSubmit('send'))->setDefaultValue('Отправить')
    );

$request = new ElementRequest();
$validator = new ElementValidator($request);
$submit = $form->getByName('send');

if ($validator->checkSubmit($submit) && $validator->handle($form)) {
    // данные валидны
}

echo $form->getHtml();
```

## Bootstrap-декоратор

```php
use Kavalhub\FormGenerator\Decorator\Bootstrap\BootstrapDecorator;

$decorator = new BootstrapDecorator($form);
echo $decorator->getHtml();
```

## CSRF-защита (opt-in)

```php
$form = (new Form('secure'))
    ->enableCsrf()
    ->addElement(/* ... */);
```

Токен сохраняется в сессии и проверяется при `ElementValidator::handle()`.

## Декларативное создание элементов

```php
use Kavalhub\FormGenerator\Fabric\ElementFabric;
use Kavalhub\FormGenerator\Form\Group;
use Kavalhub\FormGenerator\Form\InputText;

$group = ElementFabric::create([
    ElementFabric::ELEMENT => Group::class,
    ElementFabric::NAME => 'profile',
    ElementFabric::METHOD => [
        [
            ElementFabric::ADD_ELEMENT => [
                ElementFabric::ELEMENT => InputText::class,
                ElementFabric::NAME => 'name',
                ElementFabric::METHOD => [
                    ['setRequired' => true],
                ],
            ],
        ],
    ],
]);
```

## Поддерживаемые элементы

| Класс | Описание |
|-------|----------|
| `Form` | Контейнер `<form>` |
| `Group` | Группа полей с префиксом имени |
| `InputText`, `InputPassword`, `InputNumber` | Текстовые поля |
| `InputCheckbox`, `InputRadio` | Переключатели |
| `Select`, `Option` | Выпадающий список |
| `Textarea` | Многострочный ввод |
| `InputHidden`, `InputSubmit`, `Button` | Скрытые и кнопки |
| `Label`, `Nav`, `Link` | Разметка |
| `Table`, `Tr`, `Td`, `Th` | Таблицы |

## Безопасность

- Значения полей, placeholder, href и сообщения об ошибках экранируются через `HtmlEscaper`.
- `Label::setAllowHtml()` — явное разрешение HTML в подписи (по умолчанию выключено).
- `ElementRequest` читает только `$_POST` (или переданный массив в конструкторе).
- CSRF включается явно через `Form::enableCsrf()`.
- Папка `example/` — демонстрация интеграции; для БД используйте переменные окружения (см. `example/.env.example`).

## Тесты

```bash
composer install
composer test
```

## Лицензия

MIT
