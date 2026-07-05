<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Decorator\Bootstrap;

if (!class_exists(\Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator::class)) {
    throw new \RuntimeException(
        'Bootstrap decorator was moved to kavalhub/form-generator-bootstrap. '
        . 'Run: composer require kavalhub/form-generator-bootstrap'
    );
}

trigger_error(
    'Kavalhub\FormGenerator\Decorator\Bootstrap\BootstrapDecorator is deprecated since form-generator 3.3, '
    . 'use Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator from kavalhub/form-generator-bootstrap',
    E_USER_DEPRECATED,
);

class BootstrapDecorator extends \Kavalhub\FormGenerator\Bootstrap\BootstrapDecorator
{
}
