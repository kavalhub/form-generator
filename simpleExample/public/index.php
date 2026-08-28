<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Kavalhub\FormGenerator\Ajax\AjaxRequest;
use Kavalhub\FormGenerator\Ajax\ElementAjaxHandler;
use Kavalhub\FormGenerator\Ajax\ThemeAjaxRenderStrategy;
use Kavalhub\FormGenerator\Event\ElementChangedEvent;
use Kavalhub\FormGenerator\Event\ElementEventDispatcher;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Render\ElementRenderer;
use Kavalhub\FormGenerator\Render\RenderTheme;
use Kavalhub\FormGenerator\Request\ElementRequest;
use Kavalhub\FormGenerator\Storage\SessionElementStorage;
use Kavalhub\FormGenerator\Validator\ElementValidator;

// 1. Инициализация базовых компонентов
$request = new ElementRequest();
$storage = new SessionElementStorage();
$validator = new ElementValidator($request, $storage);
$dispatcher = new ElementEventDispatcher();
$renderer = new ElementRenderer();
$isAjax = AjaxRequest::isXmlHttpRequest();

// 2. Константы для избежания магических строк в коде
class Constant
{
    public const SETTINGS = 's';
    public const THEME = 'theme';
    public const TRANSPORT = 'transport';
}

// 3. Формирование шапки страницы
$header = (new Group('header'))->addElement(
        (new Label('title'))->setLabel('<h1>simpleExample</h1>')
                ->setAllowHtml(),
)
        ->addElement(
                (new Label('subtitle'))->setLabel('<p>Упрощённая demo-version</p>')
                        ->setAllowHtml(),
        );

// 4. Формирование формы настроек
$settingsSubmit = (new InputSubmit('apply'))->setDefaultValue('Применить');

$settings = (new Form(Constant::SETTINGS))->setMethod('get')
        ->enableCsrf()
        ->addElement(
                (new Group('th'))->addClass(['d-flex', 'align-items-center', 'gap-3', 'flex-wrap'])
                        ->addElement(
                                (new Label('tl'))->setLabel('Тема рендеринга:')
                                        ->addClass(['mb-0'])
                        )
                        ->addElement(
                                (new InputRadio(Constant::THEME, 'html'))->setLabel('Html')
                                        ->setChecked()
                        )
                        ->addElement((new InputRadio(Constant::THEME, 'bootstrap'))->setLabel('Bootstrap')),
        )
        ->addElement(
                (new Group('tr'))->addClass(['d-flex', 'align-items-center', 'gap-3', 'flex-wrap'])
                        ->addElement(
                                (new Label('tr'))->setLabel('Режим отправки:')
                                        ->addClass(['mb-0'])
                        )
                        ->addElement(
                                (new InputRadio(Constant::TRANSPORT, 'classic'))->setLabel('GET/POST')
                                        ->setChecked()
                        )
                        ->addElement((new InputRadio(Constant::TRANSPORT, 'ajax'))->setLabel('AJAX')),
        )
        ->addElement($settingsSubmit);

// 5. Сборка страницы и привязка диспетчера событий
$page = (new Group('p'))->addElement($header)
        ->addElement($settings)
        ->addClass(['simple-page'])
        ->setDispatcher($dispatcher)
        ->addCallbackValidator(static function ($page) {
            $settings = $page->getByName(Constant::SETTINGS);
            $values = $settings->getValueArray();

            // Извлекаем выбранный транспорт (по умолчанию 'classic')
            $transport = ($values[Constant::TRANSPORT] ?? [])[0] ?? 'classic';

            // Включаем или выключаем флаг AJAX у корневой страницы
            $page->setAjax($transport === 'ajax');
        });

// Обработчик: Реагирует на изменение и настраивает тему рендеринга
$dispatcher->addListener(ElementChangedEvent::class, function (ElementChangedEvent $event) use ($renderer): void {
    $page = $event->getElement();
    $settings = $page->getByName(Constant::SETTINGS);
    $values = $settings->getValueArray();

    // Извлекаем выбранную тему (по умолчанию 'html')
    $theme = ($values[Constant::THEME] ?? [])[0] ?? 'html';

    // Переключаем тему самого рендерера элементов
    $renderer->setTheme(
            match ($theme) {
                'bootstrap' => RenderTheme::bootstrap(),
                default => RenderTheme::plain(),
            }
    );
});

// =============================================================================
// ОСНОВНАЯ ЛОГИКА ВЫПОЛНЕНИЯ
// =============================================================================

if ($validator->checkSubmit($settingsSubmit) || $isAjax) {
    if ($validator->handle($page)) { // Заполняем элементы актуальными данными из запроса
        $dispatcher->dispatch(new ElementChangedEvent($page)); // Уведомляем систему об изменениях
    }
}

// Если это AJAX-запрос, возвращаем JSON и завершаем скрипт
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    $handler = new ElementAjaxHandler($validator, new ThemeAjaxRenderStrategy($renderer));
    echo $handler->handleForm($page)
            ->jsonEncode();

    return;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>simple demo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {font-family: system-ui, sans-serif;margin: 0;background: #f6f6f6;color: #222;}
        .simple-page {max-width: 960px;margin: 0 auto;padding: 1rem;}
        .simple-block {background: #fff;border: 1px dashed #bbb;margin-bottom: 1rem;padding: 1rem;}
        .simple-block__label {font-size: 0.75rem;text-transform: uppercase;letter-spacing: 0.05em;color: #888;margin: 0 0 0.5rem;}
        .simple-block--header {border-color: #4a90d9;}
        .simple-block--settings {border-color: #9b59b6;}
        .simple-block--table {border-color: #27ae60;min-height: 8rem;}
        .simple-block--footer {border-color: #95a5a6;font-size: 0.875rem;}
    </style>
    <script src="js/form-generator-ajax.js" defer></script>
</head>
<body>
<div <?= $page->getHtmlTrait() ?>>
    <div id="simple-ajax-message" role="status"></div>
    <header class="simple-block simple-block--header" id="layout-header">
        <p class="simple-block__label">header</p>
        <?= $renderer->html($header) ?>
    </header>

    <section class="simple-block simple-block--settings" id="layout-settings">
        <p class="simple-block__label">settings</p>
        <?= $renderer->html($settings) ?>
    </section>

    <main class="simple-block simple-block--table" id="layout-table">
        <p class="simple-block__label">table</p>
        <?php /*= $table */ ?>
    </main>

    <footer class="simple-block simple-block--footer" id="layout-footer">
        <p class="simple-block__label">footer</p>
        <?php /*= $footer */ ?>
    </footer>
</div>
</body>
</html>
