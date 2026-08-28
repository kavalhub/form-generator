<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Http;

use Kavalhub\Example\Presentation\Web\Form\Settings;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

final class DemoPreferences
{
    private const SESSION_DECORATOR = 'decorator';
    private const SESSION_TRANSPORT = 'transport';
    private const DECORATOR_KEY = 'ds_df_d';
    private const TRANSPORT_KEY = 'ds_tf_t';

    public function __construct(
        private readonly ElementValidatorInterface $validator,
    ) {
    }

    public function bindFromRequest(): void
    {
        $this->bindScalarFromRequest(self::DECORATOR_KEY, self::SESSION_DECORATOR);
        $this->bindScalarFromRequest(self::TRANSPORT_KEY, self::SESSION_TRANSPORT);

        $settings = $this->createSettingsForm();
        if ($this->validator->checkSubmit($settings->getSubmit()) && $settings->validate()) {
            $_SESSION[self::SESSION_DECORATOR] = (string)$settings->getByName('df')->getByName('d')->getValue();
            $_SESSION[self::SESSION_TRANSPORT] = (string)$settings->getByName('tf')->getByName('t')->getValue();
        }
    }

    public function decoratorMode(): DecoratorMode
    {
        $value = (string)($_SESSION[self::SESSION_DECORATOR] ?? DecoratorMode::Blade->value);

        return DecoratorMode::tryFrom($value) ?? DecoratorMode::Blade;
    }

    public function transport(): string
    {
        $value = (string)($_SESSION[self::SESSION_TRANSPORT] ?? 'ajax');

        return in_array($value, ['classic', 'ajax'], true) ? $value : 'ajax';
    }

    /**
     * @return array<string, string>
     */
    public function queryParams(): array
    {
        return [
            self::DECORATOR_KEY => $this->decoratorMode()->value,
            self::TRANSPORT_KEY => $this->transport(),
        ];
    }

    public function createSettingsForm(): Settings
    {
        $settings = new Settings($this->validator);
        $this->applyChecked(
            $settings->getByName('df'),
            'd',
            $this->decoratorMode()->value,
        );
        $this->applyChecked(
            $settings->getByName('tf'),
            't',
            $this->transport(),
        );

        return $settings;
    }

    private function bindScalarFromRequest(string $requestKey, string $sessionKey): void
    {
        if (!isset($_REQUEST[$requestKey])) {
            return;
        }
        $raw = $_REQUEST[$requestKey];
        $value = is_array($raw) ? (string)($raw[0] ?? '') : (string)$raw;
        if ($value !== '') {
            $_SESSION[$sessionKey] = $value;
        }
    }

    private function applyChecked(object $group, string $fieldName, string $activeValue): void
    {
        foreach ($group->getAll() as $child) {
            if (!$child instanceof InputRadio || $child->getName() !== $fieldName) {
                continue;
            }
            if ($child->getValue() === $activeValue) {
                $child->setChecked();
            }
        }
    }
}
