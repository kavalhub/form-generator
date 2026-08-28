<?php
declare(strict_types=1);

$templateFile = 'resources/Bootstrap/elements/Brand/Group.php';

$html = [];
foreach ($this->element->getAll() as $childElement) {
    $html[] = $this->decorateChild($childElement)->render();
}
$html = implode('', $html);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback px-2 mb2">' . $this->element->getDisplayErrors()
        . '</div>' : '';

$this->element->addClass(['px-2']);
return '<div class="border border-3 border-danger rounded p-2 mb-2">'
    . '<div class="mb-2"><span class="badge text-bg-danger">Кастомный шаблон</span> '
    . '<small class="text-muted">' . htmlspecialchars($templateFile, ENT_QUOTES) . '</small></div>'
    . '<' . $this->element->getTag() . $this->element->getHtmlTrait(['HtmlName']) . '>' . $html . '</'
    . $this->element->getTag() . '>' . $error
    . '</div>';
