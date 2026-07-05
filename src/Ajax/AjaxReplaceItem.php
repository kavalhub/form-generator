<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Ajax;

use Kavalhub\FormGenerator\Ajax\Interface\AjaxRenderStrategyInterface;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlDecoratableInterface;
use Kavalhub\FormGenerator\Html\Interface\HtmlRenderableInterface;

final class AjaxReplaceItem
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $class = null,
        private readonly ?string $error = null,
        private readonly ?string $html = null,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $data = ['ID' => $this->id];
        if ($this->class !== null && $this->class !== '') {
            $data['CLASS'] = $this->class;
        }
        if ($this->error !== null && $this->error !== '') {
            $data['ERROR'] = $this->error;
        }
        if ($this->html !== null) {
            $data['HTML'] = $this->html;
        }

        return $data;
    }

    public static function fromElement(
        ElementInterface $element,
        AjaxMode $mode,
        AjaxRenderStrategyInterface $strategy,
    ): self {
        $id = $element->getId();

        if ($mode === AjaxMode::Field) {
            return new self(
                $id,
                $strategy->fieldClass($element),
                $strategy->fieldErrorHtml($element),
            );
        }

        $html = '';
        if ($element instanceof HtmlDecoratableInterface) {
            $html = $strategy->blockHtml($element);
        } elseif ($element instanceof HtmlRenderableInterface) {
            $html = $element->render();
        }

        return new self($id, html: $html);
    }
}
