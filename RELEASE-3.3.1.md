# form-generator 3.3.1

AJAX-разметка в core, исправление `data-*` атрибутов, расширение `AbstractDecorator` для per-element шаблонов; новые пакеты Blade/Twig/Tailwind; обновление demo.

## Кратко

- **Core:** trait `HtmlAjaxBehavior` — `setAjax()`, `setUrlState()` → `data-fg-ajax`, `data-fg-url-state`
- **Fix:** `HtmlData` / `HtmlAjaxBehavior` — ведущий пробел перед атрибутами (без `<formdata-fg-ajax`)
- **Core:** `AbstractDecorator::setResourceBase()`, `decorateChild()`, `renderTemplateFile()` — поддержка Twig/Blade extension
- **Пакеты:** `form-generator-blade`, `form-generator-twig`, `form-generator-tailwind` (1.0.0)
- **Demo (example/):** мульти-декоратор shell, URL state = ключи FormData, восстановление фильтра из GET

---

## Core

### HtmlAjaxBehavior

Доступен на всех элементах с `HtmlAttributes` (`Form`, `InputText`, `InputSubmit`, …):

```php
$form->setMethod('get')
    ->setAjax(true)
    ->setUrlState('replaceState'); // 'pushState' | false

$input = (new InputText('name'))->setAjax();
```

| Метод | HTML |
|-------|------|
| `setAjax(true)` | `data-fg-ajax="true"` |
| `setUrlState('replaceState')` | `data-fg-url-state="replaceState"` |
| `setUrlState(false)` | атрибут URL state не выводится |

Тесты: `tests/Html/HtmlAjaxBehaviorTest.php`.

### Исправление data-атрибутов

`HtmlData::getHtmlData()` и `HtmlAjaxBehavior::getHtmlAjaxBehavior()` теперь выводят атрибуты с ведущим пробелом (` data-fg-ajax="true"`), как `class` и `method`. Без этого ломалась разметка: `<formdata-fg-ajax="true"`.

### AbstractDecorator

- `setResourceBase(string $path)` — база для `setPath()` на элементах (`layout/SettingsForm` → `resources/Blade/layout/SettingsForm.php`)
- `decorateChild()` — наследование `customPath` / `resourceBase` дочерним декоратором
- `renderTemplateFile()` — точка расширения для Twig (вместо `include` для `.php`)
- `resolveTemplatePaths()` — поиск по extensionless path, explicit file, directory

---

## Новые пакеты декораторов

| Пакет | Namespace | Шаблоны |
|-------|-----------|---------|
| `kavalhub/form-generator-blade` | `Kavalhub\FormGenerator\Blade\` | `resources/Blade/*.php` (Illuminate View) |
| `kavalhub/form-generator-twig` | `Kavalhub\FormGenerator\Twig\` | `*.html.twig` |
| `kavalhub/form-generator-tailwind` | `Kavalhub\FormGenerator\Tailwind\` | utility-классы Tailwind |

```bash
composer require kavalhub/form-generator-blade
composer require kavalhub/form-generator-twig
composer require kavalhub/form-generator-tailwind
```

У каждого пакета — свой `*AjaxRenderStrategy` для AJAX field/block режимов.

---

## Demo (example/, `kavalhub/form-demo`)

Не входит в пакет `form-generator`, но поставляется в репозитории как эталон интеграции.

### Мульти-декоратор

- `DemoLayout`, `DemoSettingsForm`, `DecoratorMode` — переключатель HTML / Bootstrap / Blade / Twig / Tailwind
- Шаблоны shell: `example/resources/{Bootstrap,Blade,Twig,Tailwind}/layout/`

### AJAX и URL state

- `collectPageData()` — merge всех `<form>` для POST на `ajax.php`
- URL (History API, nav, reload) — **те же ключи**, что FormData: `demoSettings_decoratorFieldset_decorator`, `fl_gc_cat[]`, `page`
- Короткий `?decorator=` **не используется** (ни запись, ни чтение)
- `FacetProductForm`: `method="get"`, `setAjax()`, `setUrlState()`; `hasFilterInputInRequest()` → восстановление фильтра из GET

### Иерархия имён

- `DemoLayout::formCardWrap()` — обёртка `Group('demoFormCard')` и при рендере, и при `validate()` / `ajax.php` (единый префикс `demoFormCard_addCategory_*` в DOM и `$_REQUEST`)

---

## Миграция с 3.3.0

Обратно совместимо. Опционально для AJAX в своём проекте:

```php
// вместо CSS-классов js-on-input / js-button-ajax
$form->setAjax(true);
$input->setAjax();
$submit->setAjax();
```

Клиентский JS читает `[data-fg-ajax="true"]` и `form[data-fg-url-state]`.

---

## Версии пакетов

| Пакет | Версия |
|-------|--------|
| `kavalhub/form-generator` | **3.3.1** |
| `kavalhub/form-generator-bootstrap` | 1.0.0 |
| `kavalhub/form-generator-blade` | 1.0.0 |
| `kavalhub/form-generator-twig` | 1.0.0 |
| `kavalhub/form-generator-tailwind` | 1.0.0 |
