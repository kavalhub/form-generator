# form-generator 4.0.0

Новый Render API вместо `AbstractDecorator` / `BootstrapDecorator::getHtml()`.

## Кратко

- **Core:** `Kavalhub\FormGenerator\Render\` — `ElementRenderer`, `RenderTheme`, `PhpTemplateEngine`, `TemplateResolver`
- **Темы:** `RenderTheme::plain()`, `RenderTheme::bootstrap()` (требует `kavalhub/form-generator-bootstrap`)
- **Удалено:** `Decorator\AbstractDecorator`, `DecoratorInterface`, shim `Decorator\Bootstrap\BootstrapDecorator`
- **Пакеты:** bootstrap/blade/twig/tailwind переведены на `*Theme` + шаблоны под Render API
- **AJAX:** `ThemeAjaxRenderStrategy` + `ElementRenderer`; `NullAjaxRenderStrategy` deprecated

---

## Рендеринг форм

### Было (3.3.x)

```php
use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator;

echo (new BootstrapDecorator($form))->getHtml();
```

### Стало (4.0)

```php
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Render\RenderTheme;

$renderer = (new ElementRenderer())->setTheme(RenderTheme::bootstrap());
echo $renderer->html($form);
```

---

## AJAX

```php
use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Bootstrap\BootstrapAjaxRenderStrategy;
use Kavalhub\FormGenerator\Validator\ElementValidator;

$handler = new ElementAjaxHandler(
    new ElementValidator($request),
    new BootstrapAjaxRenderStrategy(),
);
```

`BootstrapAjaxRenderStrategy` внутри создаёт `ElementRenderer` с `RenderTheme::bootstrap()`.

---

## Миграция пакетов тем

| Пакет | Было | Стало |
|-------|------|-------|
| `form-generator-bootstrap` | `BootstrapDecorator` | `BootstrapTheme::create()` / `RenderTheme::bootstrap()` |
| `form-generator-blade` | `BladeDecorator` | `BladeTheme` |
| `form-generator-twig` | `TwigDecorator` | `TwigTheme` |
| `form-generator-tailwind` | `TailwindDecorator` | `TailwindTheme` |

---

## Версии пакетов

| Пакет | Версия |
|-------|--------|
| `kavalhub/form-generator` | **4.0.0** |
| `kavalhub/form-generator-bootstrap` | **1.0.1** (`^4.0`) |
| `kavalhub/form-generator-blade` | 1.0.1 (`^4.0`) |
| `kavalhub/form-generator-twig` | 1.0.1 (`^4.0`) |
| `kavalhub/form-generator-tailwind` | 1.0.1 (`^4.0`) |
