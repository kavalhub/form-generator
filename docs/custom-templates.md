# Кастомные шаблоны декоратора

Декораторы рендерят элементы через шаблоны. В core: [`DecoratorInterface`](../src/Decorator/Interface/DecoratorInterface.php), [`AbstractDecorator`](../src/Decorator/AbstractDecorator.php).

И Bootstrap, и Blade используют файлы `{ClassName}.php`. Каталог `resources/Blade/` или `resources/Bootstrap/` задаёт движок.

## Структура каталогов

```
resources/
  Bootstrap/
    InputText.php                    # общий шаблон по имени класса
    CustomElements/
      InputText.php                  # кастомный конечный шаблон (переиспользуемый)
    elements/Brand/
      Group.php                      # фильтр: setPath на Group
  Blade/
    ...
```

### Индивидуальный шаблон — путь к файлу

Путь может быть любым относительно `setResourceBase()` — не обязан совпадать с именем класса или фасетом.

```php
// Фильтр: обёртка группы фасета «Бренд»
$group->setPath('elements/Brand/Group.php');

// Добавление товара: один шаблон на «Бренд», «Цвет» и др.
$input->setPath('CustomElements/InputText.php');
```

Относительный путь разрешается через `setResourceBase()` в `resources/{Blade|Bootstrap}/...`.

### Общий шаблон по типу элемента

Файл `resources/Blade/InputText.php` подхватывается для всех `InputText` без своего `setPath()`.

## Demo

- **Фильтр** (`FacetProductForm`): `elements/Brand/Group.php` — красная рамка
- **Товар** (`AddProductForm`): `CustomElements/InputText.php` — зелёная рамка для фасета «Бренд»
- **Общий** `InputText.php` в корне `resources/{Blade|Bootstrap|Tailwind}/` — синяя/primary рамка
- **Twig** — шаблоны `*.html.twig`, стили `form-twig.css`
- **Tailwind** — utility-классы, CDN Tailwind в demo

### Путь без расширения

`setPath('CustomElements/InputText')` — декоратор подставит `.php` или `.html.twig` автоматически.

### Demo shell

Оболочка demo ([`DemoLayout.php`](../../example/Env/DemoLayout.php)) — навигация, кнопки, переключатели — тоже рендерится через `FormRenderer`. Шаблоны в `resources/{Decorator}/layout/`.

Настройки demo — отдельный [`DemoSettingsForm`](../../example/Env/DemoSettingsForm.php) (`<form id="demoSettings" method="get">`) на одной странице с рабочей формой (`fl`, `add*`). JS перед AJAX сливает `FormData` **всех** `<form>` на странице (`collectPageData()`); сервер читает decorator и transport **только** по `getFormName()` (например `demoSettings_decoratorFieldset_decorator`). **URL state** (History API, nav, reload) — те же ключи, что в FormData; пример: `?demoSettings_decoratorFieldset_decorator=bootstrap&demoSettings_transportFieldset_transport=ajax&fl_gc_cat[]=1&page=filter`.

Смена декоратора — полная навигация с query string из всех форм (`navigateWithPageState` в [`form-demo-ajax.js`](../../form/public/js/form-demo-ajax.js)). Nav-ссылки и редиректы строятся через [`DemoSettingsForm::stateQueryParams()`](../../example/Env/DemoSettingsForm.php).

Фильтр [`FacetProductForm`](../../example/Env/FacetProductForm.php) — `method="get"`, `setAjax()` + `setUrlState('replaceState')`: после AJAX POST на `ajax.php` клиент обновляет URL через History API; при перезагрузке или смене Twig/Bootstrap `index.php` восстанавливает фильтр из `fl_*` в query string (`hasFilterInputInRequest()` → `applyFilter(true)`).

## Содержимое шаблона

**Bootstrap:** `$this`, `$this->element`, `$this->decorateChild($child)`.

**Blade:** `$element`, `$decorator`, `$decorator->decorateChild($child)`.
