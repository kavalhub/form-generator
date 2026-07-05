# form-generator 3.0.0

Major release: разделение доменной модели и HTML-представления.

## Кратко

В версии 3.0 библиотека разделена на два слоя:

- **`Element/`** — дерево элементов, значения, валидация, CSRF-логика (без HTML)
- **`Html/`** — виджеты, HTML-трейты, рендеринг в браузер

Каталог `Form/` переименован в `Html/` — это точнее отражает его роль.

---

## Breaking changes

### Namespace виджетов

| Было (2.x) | Стало (3.0) |
|------------|-------------|
| `Kavalhub\FormGenerator\Form\*` | `Kavalhub\FormGenerator\Html\*` |
| `Kavalhub\FormGenerator\Table\*` | `Kavalhub\FormGenerator\Html\Table\*` |
| `Kavalhub\FormGenerator\Util\HtmlEscaper` | `Kavalhub\FormGenerator\Html\Util\HtmlEscaper` |

### API рендеринга

| Было | Стало |
|------|-------|
| `$form->getHtml()` | `$form->render()` |
| `$input->getHtml()` | `$input->render()` |

Декораторы (`BootstrapDecorator` и др.) по-прежнему используют **`getHtml()`** — их API не менялся.

### Доменный `Element`

Из базового класса `Element` убрано всё, что относится к HTML:

- `$tag`, `getTag()`
- `ClassList` (`addClass`, `removeClass`)
- `Path` (путь к шаблону декоратора)
- `getHtml()` и все `Html*` трейты

`ElementInterface` больше не содержит `getHtml()`.

---

## Новая архитектура

### Доменный слой (`Element/`)

- Дерево: `parent`, `addElement`, `getByName`, `getValueArray`
- Доменные трейты: `Name`, `Value`, `Required`, `Identifiable`
- Валидация: `Valid`, `Error`, `CallbackValidator`
- CSRF: генерация и проверка токена (без создания HTML-поля)
- Интерфейсы: `SkipsValueCollection`, `CsrfProtectable`

### HTML-слой (`Html/`)

Базовые классы-прослойки:

| Класс | Назначение |
|-------|------------|
| `HtmlElement` | `tag`, `ClassList`, `Path`, `HtmlAttributes` |
| `HtmlElementWithName` | именованный HTML-элемент |
| `HtmlElementWithValue` | поле со значением + `HtmlInputAttributes` |
| `HtmlCompositeElement` | контейнер (`Group`, `Form`, `Table`) |
| `HtmlCompositeElementWithValue` | контейнер со значением (`Select`, `Nav`) |

Интерфейсы:

- `HtmlRenderableInterface` — `render(string $innerHtml = ''): string`
- `HtmlDecoratableInterface` — для декораторов (`addClass`, `getTag`, `getHtmlTrait`, …)

Все виджеты (`InputText`, `Form`, `Select`, …) наследуются от соответствующих `Html*`-баз.

---

## Миграция с 2.x

### 1. Обновить зависимость

```json
"kavalhub/form-generator": "^3.0"
```

### 2. Заменить импорты

```php
// Было
use Kavalhub\FormGenerator\Form\Form;
use Kavalhub\FormGenerator\Form\InputText;
use Kavalhub\FormGenerator\Table\Td;

// Стало
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\InputText;
use Kavalhub\FormGenerator\Html\Table\Td;
```

### 3. Заменить вызовы рендеринга

```php
// Было
echo $form->getHtml();

// Стало
echo $form->render();
```

### 4. Декораторы — без изменений

```php
echo (new BootstrapDecorator($inputText))->getHtml();
```

### 5. HtmlEscaper

```php
// Было
use Kavalhub\FormGenerator\Util\HtmlEscaper;

// Стало
use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;
```

---

## Что не изменилось

- Request / Validator / Factory — API сохранён, обновлены только внутренние импорты
- Bootstrap-декоратор — по-прежнему в основном пакете
- Laravel-адаптер (`packages/laravel`) — совместим с 3.0
- Пример приложения в `example/`

---

## Требования

- PHP ^8.2

---

## Полный список изменений

- Разделение `Element` (домен) и `Html` (представление)
- Переименование `src/Form/` → `src/Html/`
- Перенос `src/Table/` → `src/Html/Table/`
- Перенос 31 HTML-трейта из `Element/Trait/` в `Html/Trait/`
- Разделение dual-purpose трейтов (`HtmlName` → `Name` + `HtmlName`, и т.д.)
- Удаление обратных зависимостей `Element` → `Form` (маркеры `SkipsValueCollection`, `CsrfProtectable`)
- CSRF-поле (`InputHidden`) создаётся в `Html\Form::enableCsrf()`, а не в доменном трейте
- Декораторы принимают `HtmlDecoratableInterface`
- `TraitCollector` — рекурсивный сбор вложенных HTML-трейтов
