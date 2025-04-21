<?php
declare(strict_types=1);

if ($this->element->isRequired()) {
    $this->element->addClass(['required']);
}

return $this->element->getHtml();
