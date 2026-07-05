<?php
declare(strict_types=1);

$errorHtml = !empty($this->element->getError())
    ? '<div class="mt-1 text-sm text-red-600">' . $this->element->getDisplayErrors() . '</div>'
    : '';
$this->element->addClass(['h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500']);

return '<div class="mb-3"><label for="' . $this->element->getId() . '">' . $this->element->getTitle() . '</label>'
    . $this->element->render() . $errorHtml . '</div>';
