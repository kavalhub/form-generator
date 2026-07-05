<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Html\Trait;

use Kavalhub\FormGenerator\Html\Util\HtmlEscaper;

trait HtmlAjaxBehavior
{
    protected bool $ajaxEnabled = false;
    protected ?string $urlState = null;

    public function setAjax(bool $enabled = true): self
    {
        $this->ajaxEnabled = $enabled;

        return $this;
    }

    public function isAjax(): bool
    {
        return $this->ajaxEnabled;
    }

    /**
     * @param 'replaceState'|'pushState'|false|null $mode
     */
    public function setUrlState(string|false|null $mode = 'replaceState'): self
    {
        if ($mode === false || $mode === null || $mode === '') {
            $this->urlState = null;

            return $this;
        }

        $this->urlState = $mode;

        return $this;
    }

    public function getUrlState(): ?string
    {
        return $this->urlState;
    }

    protected function getHtmlAjaxBehavior(): string
    {
        $html = [];
        if ($this->ajaxEnabled) {
            $html[] = ' data-fg-ajax="true"';
        }
        if ($this->urlState !== null && $this->urlState !== '') {
            $html[] = ' data-fg-url-state="' . HtmlEscaper::escapeAttribute($this->urlState) . '"';
        }

        return implode('', $html);
    }
}
