# form-generator 3.3.0

Bootstrap-декоратор вынесен в отдельный пакет; AJAX-слой больше не зависит от конкретной темы.

## Кратко

- **Core:** `DecoratorInterface`, `AbstractDecorator`, `AjaxRenderStrategyInterface`, `NullAjaxRenderStrategy`
- **Новый пакет:** `kavalhub/form-generator-bootstrap` (`packages/bootstrap/`)
- **3.3:** старый namespace `Decorator\Bootstrap\BootstrapDecorator` deprecated (shim при установленном bootstrap-пакете)
- **4.0:** shim и каталог `src/Decorator/Bootstrap/` будут удалены

---

## Breaking changes

### Bootstrap — отдельный пакет

```bash
composer require kavalhub/form-generator-bootstrap
```

| Было (3.2) | Стало (3.3) |
|------------|-------------|
| `Kavalhub\FormGenerator\Decorator\Bootstrap\BootstrapDecorator` | `Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator` |
| Шаблоны в `vendor/.../src/Decorator/Bootstrap/` | `vendor/.../form-generator-bootstrap/templates/` |

Старый класс до 4.0 доступен с `E_USER_DEPRECATED`, если установлен bootstrap-пакет.

### ElementAjaxHandler — обязательная стратегия рендеринга

```php
// 3.2
$handler = new ElementAjaxHandler($validator);

// 3.3
use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;

$handler = new ElementAjaxHandler(
    $validator,
    new BootstrapAjaxRenderStrategy(),
);
```

Без темы:

```php
use Kavalhub\FormGenerator\Ajax\NullAjaxRenderStrategy;

$handler = new ElementAjaxHandler($validator, new NullAjaxRenderStrategy());
```

### AjaxReplaceItem::fromElement()

Добавлен обязательный третий аргумент `AjaxRenderStrategyInterface`.

---

## Улучшения

### AbstractDecorator::setTemplate()

`setTemplate()` задаёт **дополнительный** каталог переопределений; шаблоны пакета остаются fallback. Подробнее: [docs/custom-templates.md](docs/custom-templates.md).

### BootstrapAjaxRenderStrategy

Инкапсулирует Bootstrap-разметку для AJAX field/block режимов (`is-invalid`, `invalid-feedback`, HTML блоков).

---

## Миграция demo-приложения

```json
{
    "require": {
        "kavalhub/form-generator": "^3.3",
        "kavalhub/form-generator-bootstrap": "^1.0"
    }
}
```

Обёртка `FormRenderer` и каталог `resources/form-templates/` для правок дизайнерами — см. `kavalhub/form-demo`.

---

## Версии пакетов

| Пакет | Версия |
|-------|--------|
| `kavalhub/form-generator` | 3.3.0 |
| `kavalhub/form-generator-bootstrap` | 1.0.0 |
