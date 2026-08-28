<?php

declare(strict_types=1);

namespace Kavalhub\FormGenerator\Validator;

use Kavalhub\FormGenerator\Element\ElementWithValue;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\CsrfHiddenInput;
use Kavalhub\FormGenerator\Html\InputSubmit;
use Kavalhub\FormGenerator\Request\Interface\RequestInterface;
use Kavalhub\FormGenerator\Storage\Interface\ElementStorageInterface;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

class ElementValidator implements ElementValidatorInterface
{
    private ?bool $valid = null;
    private array $checkList = [];

    public function __construct(
        private readonly RequestInterface $request,
        private readonly ?ElementStorageInterface $storage = null
    ) {
    }

    public function checkSubmit(InputSubmit $submit): bool
    {
        return !empty($this->request->get($submit->getFormName()));
    }

    public function handle(ElementInterface $element): bool
    {
        $this->checkList = [];

        if (method_exists($element, 'clearErrors')) {
            $element->clearErrors();
        }

        // 1. Специальная логика для CSRF
        if ($element instanceof CsrfHiddenInput) {
            return $this->handleCsrf($element);
        }

        // 2. Рекурсия для композитов
        if ($element->getComposite()) {
            foreach ($element->getComposite()->getAll() as $composite) {
                $this->checkList[] = (new self($this->request, $this->storage))->handle($composite);
            }
        }
        $this->request->setValue($element);
        $this->storage->setDefaultValue($element);

        // 3. Валидация required
        if ($this->validateRequired($element) === false) {
            $this->checkList[] = false;
        }

        // 4. Кастомные валидаторы
        foreach ($element->getCallbackValidatorList() as $callbackValidator) {
            $this->checkList[] = $callbackValidator($element);
        }

        // 5. Итоговая проверка
        $isValid = !in_array(false, $this->checkList, true);

        if ($isValid) {
            $element->setValid();
            $this->valid = true;
            $this->storage->set($element);

            return true;
        }

        $this->valid = false;
        return false;
    }

    private function handleCsrf(CsrfHiddenInput $element): bool
    {
        $name = $element->getName();
        $storedToken = null;

        // Пытаемся получить существующий токен
        if ($this->storage !== null) {
            $storedToken = (string)$this->storage->get($name);
        }

        // Если токена нет, генерируем новый
        if ($storedToken === '' || $storedToken === null) {
            $storedToken = bin2hex(random_bytes(32));
            if ($this->storage !== null) {
                $this->storage->set($name, $storedToken);
            }
        }

        // Устанавливаем токен в элемент для корректного рендеринга формы
        $element->setStoredValue($storedToken);

        // Проверяем токен из запроса
        $requestValue = $this->request->get($name);
        $submittedToken = is_array($requestValue) ? ($requestValue[0] ?? '') : ($requestValue ?? '');

        if (!$element->validateToken((string)$submittedToken, $storedToken)) {
            $element->addError(['Неверный CSRF-токен']);
            $this->valid = false;
            return false;
        }

        $element->setValid();
        $this->valid = true;

        // После успешной валидации CSRF-токен регенерируют (защита от replay-атак)
        $newToken = bin2hex(random_bytes(32));
        if ($this->storage !== null) {
            $this->storage->set($name, $newToken);
        }
        $element->setStoredValue($newToken);

        return true;
    }

    private function validateRequired(ElementInterface $element): ?bool
    {
        if (!$element->isRequired() || !$element instanceof ElementWithValue) {
            return null;
        }
        if (empty($element->getValue())) {
            $element->addError(['Поле должно быть заполнено']);
            return false;
        }
        return true;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }
}