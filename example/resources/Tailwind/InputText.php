<?php
declare(strict_types=1);

$templateFile = 'resources/Tailwind/InputText.php';
$this->element->addClass(['w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200', 'border-indigo-500']);
$error = !empty($this->element->getError())
    ? '<div class="mt-1 text-sm text-red-600">' . $this->element->getDisplayErrors() . '</div>' : '';
return '<div class="mb-3 border-l-4 border-indigo-500 pl-3">'
    . '<div class="mb-1"><span class="rounded-full bg-indigo-600 px-2 py-0.5 text-xs font-semibold uppercase text-white">Кастомный шаблон</span> '
    . '<span class="font-mono text-xs text-slate-500">' . htmlspecialchars($templateFile, ENT_QUOTES) . '</span></div>'
    . $this->element->render() . $error . '</div>';
