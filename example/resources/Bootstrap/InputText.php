<?php
declare(strict_types=1);

$templateFile = 'resources/Bootstrap/InputText.php';

$this->element->addClass(['form-control', 'border-primary']);
$error =
    !empty($this->element->getError()) ? '<div class="invalid-feedback">' . $this->element->getDisplayErrors()
        . '</div>' : '';
return '<div class="form-group border-start border-3 border-primary ps-2">'
    . '<div class="mb-1"><span class="badge text-bg-primary">Кастомный шаблон</span> '
    . '<small class="text-muted">' . htmlspecialchars($templateFile, ENT_QUOTES) . '</small></div>'
    . $this->element->render() . $error
    . '</div>';
