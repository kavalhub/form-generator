<?php
declare(strict_types=1);

if (!in_array('page-link', $this->element->getClassList(), true)) {
    $this->element->addClass(['nav-link']);
}

return $this->element->render();
