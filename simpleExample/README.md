# simpleExample

Самодостаточная упрощённая demo для `kavalhub/form-generator`.

Скопируйте каталог к себе в проект (или откройте `public/` прямо из `vendor/.../simpleExample/`) и укажите web-серверу document root на `simpleExample/public`.

## Установка

```bash
composer require kavalhub/form-generator kavalhub/form-generator-bootstrap
```

Затем либо:

```bash
cp -r vendor/kavalhub/form-generator/simpleExample ./simpleExample
# document root → ./simpleExample/public
```

либо откройте `vendor/kavalhub/form-generator/simpleExample/public` напрямую (bootstrap найдёт `vendor/autoload.php` выше по дереву).

В form-demo URL `/simple/` — тонкий прокси на этот каталог.

## Структура

```
simpleExample/
├── bootstrap.php
├── public/
│   ├── index.php
│   └── js/simple-ajax.js
├── layout/
│   ├── html/simple.php
│   └── bootstrap/simple.php
└── Ping.php
```

| Путь | Описание |
|------|----------|
| `public/index.php` | Entrypoint: header + settings, переключение html/bootstrap |
| `public/js/simple-ajax.js` | Глобальный обработчик: от `this` собирает все `[data-fg-ajax]` в форме и POST на `form[action]` или текущий URL |
| `layout/html/simple.php` | Страница без фреймворка, слоты: header, settings, table, footer |
| `layout/bootstrap/simple.php` | То же + Bootstrap 5 CDN |

Layout-шаблоны подключаются через `Group::setPath()` и `ElementRenderer`, не через прямой `require`.

Namespace `Kavalhub\SimpleExample\` — для будущих классов демо (`Ping.php` — заглушка).
