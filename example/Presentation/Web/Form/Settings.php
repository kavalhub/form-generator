<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Form;

use Kavalhub\Example\Presentation\Http\DecoratorMode;
use Kavalhub\FormGenerator\Html\Form;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\InputRadio;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

final class Settings extends Form
{
    private Group $decoratorGroup;

    private Group $transportGroup;

    private InputSubmit $submit;

    public function __construct(private readonly ElementValidatorInterface $validator)
    {
        parent::__construct('ds');
        $this->setMethod('get');

        $this->decoratorGroup = new Group('df');
        $this->decoratorGroup->addElement(
            (new Label('dl'))->setLabel('Режим рендеринга'),
        );
        foreach (DecoratorMode::cases() as $case) {
            $this->decoratorGroup->addElement(
                (new InputRadio('d', $case->value))
                    ->setLabel(DecoratorMode::decoratorLabel($case)),
            );
        }
        $this->addElement($this->decoratorGroup);

        $this->transportGroup = new Group('tf');
        $this->transportGroup->addElement(
            (new Label('tl'))->setLabel('Режим отправки'),
        );
        $this->transportGroup->addElement(
            (new InputRadio('t', 'classic'))
                ->setLabel('Классическая (GET/POST + reload)'),
        );
        $this->transportGroup->addElement(
            (new InputRadio('t', 'ajax'))
                ->setLabel('AJAX (без перезагрузки)'),
        );
        $this->addElement($this->transportGroup);

        $this->submit = new InputSubmit('setting');

        $this->addElement($this->submit);
    }

    public function getSubmit(): InputSubmit
    {
        return $this->submit;
    }

    public function validate(): bool
    {
        return $this->validator->checkSubmit($this->submit) && $this->validator->handle($this);
    }
}
