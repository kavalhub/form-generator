<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Util\ElementDataCollector;
use Kavalhub\FormGenerator\Validator\Interface\ElementValidatorInterface;

final class ElementAjaxHandler
{
    public function __construct(
        private readonly ElementValidatorInterface $validator,
        private readonly AjaxRenderStrategyInterface $renderStrategy,
    ) {
    }

    public function handleField(ElementInterface $root, string $targetId): AjaxResponse
    {
        $response = new AjaxResponse();
        $element = ElementDataCollector::findById($root, $targetId);
        if ($element === null) {
            return $response;
        }

        $this->validator->handle($element);
        $response->addReplace(AjaxReplaceItem::fromElement($element, AjaxMode::Field, $this->renderStrategy));

        return $response;
    }

    public function handleBlock(ElementInterface $block): AjaxResponse
    {
        $response = new AjaxResponse();
        $response->addReplace(AjaxReplaceItem::fromElement($block, AjaxMode::Block, $this->renderStrategy));

        return $response;
    }

    public function handleForm(ElementInterface $root, ?string $replaceTargetId = null): AjaxResponse
    {
        $this->validator->handle($root);

        $target = $replaceTargetId !== null
            ? ElementDataCollector::findById($root, $replaceTargetId)
            : $root;
        if ($target === null) {
            $target = $root;
        }

        return $this->handleBlock($target);
    }

    public function validator(): ElementValidatorInterface
    {
        return $this->validator;
    }
}
