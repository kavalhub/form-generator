# Form Generator - Структура проекта

## Архитектура
Clean Architecture (DDD) с использованием Symfony EventDispatcher

## Основные директории

### src/ - Ядро проекта (Composer пакет)
- `src/Element/` - Классы элементов формы (Input, Select, Radio и т.д.)
- `src/Validator/` - Валидаторы элементов
- `src/Renderer/` - Рендереры (Bootstrap, Tailwind, Blade, Twig)
- `src/Domain/Event/` - События и диспетчер
- `src/Api/` - PSR интерфейсы

### simpleExample/ - Пример использования
- `simpleExample/public/index.php` - Точка входа
- `simpleExample/bootstrap.php` - НЕ СУЩЕСТВУЕТ (используется Composer autoload)

### vendor/ - ЗАПРЕЩЕНО ИСКАТЬ
- Это зависимости Composer (Symfony, etc.)
- НЕ анализировать файлы из vendor/

## Как работает диспетчер событий
1. Элемент формы генерирует событие (например, ElementCreatedEvent)
2. ElementEventDispatcher передаёт событие слушателям
3. Слушатели реагируют (например, меняют рендерер)

## Важные файлы
- `src/Domain/Event/ElementEventDispatcher.php` - Диспетчер событий
- `src/Domain/Event/ElementEvent.php` - Базовый класс события
- `simpleExample/public/index.php` - Пример использования

## Правила
- НЕ искать файлы в vendor/
- НЕ предполагать наличие bootstrap.php
- Использовать Composer autoload (vendor/autoload.php)
- Проект НЕ использует Laravel (только Blade рендерер как опция)
