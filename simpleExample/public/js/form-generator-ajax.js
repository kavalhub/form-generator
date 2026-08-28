(function () {
    // Делаем функцию асинхронной для удобной работы с fetch и await
    async function send(el) {
        const form = el.form || el.closest('form');
        const url = (form && form.getAttribute('action')) || window.location.href;
        const scope = form || document;
        const body = new FormData();

        scope.querySelectorAll('[data-fg-ajax]').forEach((element) => {
            if (!element.name || element.disabled) {
                return;
            }
            if ((element.type === 'checkbox' || element.type === 'radio') && !element.checked) {
                return;
            }
            body.append(element.name, element.value);
        });

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body,
            });

            if (!response.ok) {
                console.error('Ошибка сети:', response.statusText);
                return;
            }

            // Парсим JSON-ответ от сервера
            const data = await response.json();

            // Проверяем наличие маркера REPLACE и что это массив
            if (data && Array.isArray(data.REPLACE)) {
                data.REPLACE.forEach((item) => {
                    // Ищем элемент по ID
                    const targetEl = document.getElementById(item.ID);

                    if (targetEl) {
                        // Вариант 1: Простая замена всего элемента (включая его самого)
                        targetEl.outerHTML = item.HTML;

                        /*
                        // Вариант 2 (более современный и безопасный для некоторых фреймворков):
                        // const temp = document.createElement('div');
                        // temp.innerHTML = item.HTML.trim();
                        // targetEl.replaceWith(temp.firstElementChild);
                        */
                    } else {
                        console.warn(`Элемент с ID "${item.ID}" не найден в DOM для замены.`);
                    }
                });
            }
        } catch (error) {
            console.error('Ошибка при обработке AJAX-запроса:', error);
        }
    }

    function fromTarget(event) {
        const el = event.target;
        if (!(el instanceof Element) || !el.matches('[data-fg-ajax]')) {
            return;
        }
        // Передаем элемент как аргумент, так как send теперь не использует this
        send(el);
    }

    document.addEventListener('change', fromTarget);
    document.addEventListener('input', fromTarget);
    document.addEventListener('click', (event) => {
        const el = event.target instanceof Element
                   ? event.target.closest('[data-fg-ajax]')
                   : null;

        if (!el || (el.type !== 'submit' && el.tagName !== 'BUTTON')) {
            return;
        }
        event.preventDefault();
        send(el);
    });
})();