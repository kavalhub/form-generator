<?php
declare(strict_types=1);

$this->element->addClass(['inline-flex cursor-pointer items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300']);

return '<div class="mb-3 space-y-1">' . $this->element->render() . '</div>';
