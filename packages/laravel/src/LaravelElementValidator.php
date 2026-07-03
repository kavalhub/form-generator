<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Laravel;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Form\InputSubmit;
use Kavalhub\FormGenerator\Request\Interface\RequestInterface;
use Kavalhub\FormGenerator\Util\ElementDataCollector;
use Kavalhub\FormGenerator\Validator\ElementValidator;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

/**
 * Гибридный валидатор: bind/required/callback/CSRF через core ElementValidator,
 * правила Laravel — через illuminate/validation.
 */
final class LaravelElementValidator implements ElementValidatorInterface
{
    private ?bool $valid = null;

    /** @var array<string, string|array<int, string>> */
    private array $rules = [];

    /** @var array<string, string> */
    private array $messages = [];

    private readonly ElementValidator $coreValidator;

    public function __construct(
        RequestInterface $request,
        private readonly ValidatorFactory $validatorFactory,
    ) {
        $this->coreValidator = new ElementValidator($request);
    }

    /**
     * @param array<string, string|array<int, string>> $rules
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @param array<string, string> $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function checkSubmit(InputSubmit $submit): bool
    {
        return $this->coreValidator->checkSubmit($submit);
    }

    public function handle(ElementInterface $element): bool
    {
        $coreValid = $this->coreValidator->handle($element);
        $this->valid = $coreValid;

        if (!$coreValid || $this->rules === []) {
            return $coreValid;
        }

        $data = ElementDataCollector::collectByFormName($element);
        $validator = $this->validatorFactory->make($data, $this->rules, $this->messages);

        if ($validator->fails()) {
            ElementDataCollector::applyErrors($element, $validator->errors()->messages());
            $this->valid = false;

            return false;
        }

        $this->valid = true;

        return true;
    }

    public function isValid(): ?bool
    {
        return $this->valid ?? $this->coreValidator->isValid();
    }

    public function coreValidator(): ElementValidator
    {
        return $this->coreValidator;
    }
}
