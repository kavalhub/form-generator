<?php
declare(strict_types=1);

$templateFile = 'resources/Bootstrap/CustomElements/InputText.php';

$this->element->addClass(['form-control', 'border-success']);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors()
        . '</div>' : '';
return '<div class="form-group border-start border-3 border-success ps-2">'
    . '<div class="mb-1"><span class="badge text-bg-success">Кастомный шаблон</span> '
    . '<small class="text-muted">' . htmlspecialchars($templateFile, ENT_QUOTES) . '</small></div>'
    . $this->element->render() . $error
    . '</div>';
