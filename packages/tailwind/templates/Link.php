<?php
declare(strict_types=1);

if (in_array('page-link', $this->element->getClassList(), true)) {
    $this->element->addClass(['px-3 py-1 rounded border border-gray-300 hover:bg-gray-50']);
} else {
    $this->element->addClass(['font-medium text-indigo-600 hover:text-indigo-800']);
}

return $this->element->render();
