<?php
declare(strict_types=1);

$templateFile = 'resources/Tailwind/elements/Brand/Group.php';
$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->getHtml();
}
$html = implode('', $html);
$error = !empty($this->element->getError())
    ? '<div class="mt-1 text-sm text-red-600 px-2">' . $this->element->getDisplayErrors() . '</div>' : '';
$this->element->addClass(['p-3']);
return '<div class="mb-3 rounded-xl border-2 border-rose-300 bg-gradient-to-b from-white to-rose-50 p-3 shadow-sm">'
    . '<div class="mb-2"><span class="rounded-full bg-rose-600 px-2 py-0.5 text-xs font-semibold uppercase text-white">Кастомный шаблон</span> '
    . '<span class="font-mono text-xs text-slate-500">' . htmlspecialchars($templateFile, ENT_QUOTES) . '</span></div>'
    . '<' . $this->element->getTag() . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $this->element->getTag() . '>' . $error . '</div>';
