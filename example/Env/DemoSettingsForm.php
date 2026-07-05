<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Html\Label;

final class DemoSettingsForm extends Form
{
    public const NAME = 'demoSettings';

    public function __construct(DecoratorMode $mode)
    {
        parent::__construct(self::NAME);
        $this->setMethod('get');
        $this->setPath('layout/SettingsForm');

        $decoratorGroup = (new Group('decoratorFieldset'))->setPath('layout/Fieldset');
        $decoratorGroup->addElement((new Label('decoratorLegend'))->setLabel('Режим рендеринга'));
        foreach (DecoratorMode::cases() as $case) {
            $radio = (new InputRadio('decorator', $case->value))
                ->setId('decorator-' . $case->value)
                ->setLabel(self::decoratorLabel($case));
            if ($mode === $case) {
                $radio->setChecked();
            }
            $decoratorGroup->addElement($radio);
        }
        $this->addElement($decoratorGroup);

        $transportGroup = (new Group('transportFieldset'))->setPath('layout/Fieldset');
        $transportGroup->addElement((new Label('transportLegend'))->setLabel('Режим отправки'));
        $transport = self::transportFromRequest();
        $classicRadio = (new InputRadio('transport', 'classic'))
            ->setId('transport-classic')
            ->setLabel('Классическая (POST + reload)');
        $ajaxRadio = (new InputRadio('transport', 'ajax'))
            ->setId('transport-ajax')
            ->setLabel('AJAX (без перезагрузки)');
        if ($transport === 'classic') {
            $classicRadio->setChecked();
        } else {
            $ajaxRadio->setChecked();
        }
        $transportGroup->addElement($classicRadio);
        $transportGroup->addElement($ajaxRadio);
        $this->addElement($transportGroup);

        $this->addElement(
            (new Group('demoAjaxMessage'))
                ->setId('demo-ajax-message')
                ->setPath('layout/AjaxMessage')
        );
    }

    public function decoratorRequestKey(): string
    {
        $field = $this->getByName('decorator');

        return method_exists($field, 'getFormName') ? $field->getFormName() : self::NAME . '_decorator';
    }

    public function transportRequestKey(): string
    {
        $field = $this->getByName('transport');

        return method_exists($field, 'getFormName') ? $field->getFormName() : self::NAME . '_transport';
    }

    public static function transportFromRequest(): string
    {
        $key = self::NAME . '_transportFieldset_transport';
        $raw = $_REQUEST[$key] ?? 'ajax';
        $value = is_array($raw) ? (string)($raw[0] ?? '') : (string)$raw;

        return in_array($value, ['classic', 'ajax'], true) ? $value : 'ajax';
    }

    /**
     * @return array<string, string>
     */
    public static function stateQueryParams(DecoratorMode $decorator, ?string $transport = null): array
    {
        $form = new self($decorator);

        return [
            $form->decoratorRequestKey() => $decorator->value,
            $form->transportRequestKey() => $transport ?? self::transportFromRequest(),
        ];
    }

    private static function decoratorLabel(DecoratorMode $mode): string
    {
        return match ($mode) {
            DecoratorMode::Html => 'HTML (без декоратора)',
            DecoratorMode::Bootstrap => 'Bootstrap',
            DecoratorMode::Blade => 'Blade',
            DecoratorMode::Twig => 'Twig',
            DecoratorMode::Tailwind => 'Tailwind',
        };
    }
}
