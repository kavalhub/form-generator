<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Layout;

use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\Label;

final class Header extends Group
{
    public function __construct()
    {
        parent::__construct('demoHeader');
        $this->addElement(
            (new Label('demoTitle'))->setLabel('<h1 class="demo-title">form-generator demo</h1>')->setAllowHtml(),
        );
        $this->addElement(
            (new Label('demoSubtitle'))->setLabel(
                '<p class="demo-subtitle">Демонстрация пакета <code>kavalhub/form-generator</code> '
                . '(namespace <code>Kavalhub\\Example</code>)</p>',
            )->setAllowHtml(),
        );
    }
}
