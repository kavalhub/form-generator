<?php
declare(strict_types=1);

namespace Kavalhub\Example\Presentation\Web\Layout;

use Kavalhub\Example\Presentation\Http\DecoratorMode;
use Kavalhub\Example\Presentation\Http\DemoPreferences;
use Kavalhub\Example\Presentation\Web\Form\Settings;
use Kavalhub\Example\Presentation\Web\Render\FormRenderer;
use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Button;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Link;

final class DemoLayout
{
    public const FORM_CARD_GROUP = 'demoFormCard';

    public function __construct(
        private readonly FormRenderer $renderer,
        private readonly DemoPreferences $preferences,
    ) {
    }

    public function header(DecoratorMode $mode): string
    {
        return $this->renderer->html(new Header(), $this->shellMode($mode));
    }

    public function toolbar(DecoratorMode $mode): string
    {
        $group = (new Group('demoToolbar'))->setPath('layout/Toolbar');
        $group->addElement(
            (new Button('seed'))
                ->setType('button')
                ->setId('btn-seed-data')
                ->setLabel('Добавить тестовые данные')
                ->addClass($this->buttonClasses($mode, 'seed'))
        );
        $group->addElement(
            (new Link('apiDocs', '/api-docs.html', 'Документация JSON API'))
                ->addClass($this->buttonClasses($mode, 'link'))
        );

        return $this->renderer->html($group, $this->shellMode($mode));
    }

    /**
     * @param array<string, string> $items
     */
    public function nav(string $activePage, DecoratorMode $mode, array $items): string
    {
        $group = (new Group('demoNav'))->setPath('layout/Nav');
        foreach ($items as $key => $label) {
            $params = array_merge(
                $this->preferences->queryParams(),
                ['page' => $key],
            );
            $link = (new Link('nav_' . $key, '?' . http_build_query($params), $label))
                ->addClass($this->navLinkClasses($mode, $key === $activePage));
            $group->addElement($link);
        }

        return $this->renderer->html($group, $this->shellMode($mode));
    }

    public function flash(string $message, DecoratorMode $mode): string
    {
        if ($message === '') {
            return '';
        }

        $group = (new Group('demoFlash'))->setPath('layout/Flash');
        $group->addElement((new Label('flashMessage'))->setLabel($message));

        return $this->renderer->html($group, $this->shellMode($mode));
    }

    public function settings(DecoratorMode $mode, Settings $settings): string
    {
        return $this->renderer->html($settings, $this->shellMode($mode));
    }

    public function formCardWrap(ElementInterface $form): Group
    {
        return $this->ensureFormCardWrap($form);
    }

    public function formCard(?ElementInterface $form, DecoratorMode $mode): string
    {
        if ($form === null) {
            return '';
        }

        $card = $this->ensureFormCardWrap($form);
        if ($card->getPath() === '') {
            $card->setPath('layout/FormCard');
        }

        return $this->renderer->html($card, $mode);
    }

    private function ensureFormCardWrap(ElementInterface $form): Group
    {
        $parent = $form->getParent();
        if ($parent instanceof Group && $parent->getName() === self::FORM_CARD_GROUP) {
            return $parent;
        }

        return (new Group(self::FORM_CARD_GROUP))->addElement($form);
    }

    private function shellMode(DecoratorMode $mode): DecoratorMode
    {
        return $mode === DecoratorMode::Html ? DecoratorMode::Bootstrap : $mode;
    }

    /**
     * @return list<string>
     */
    private function buttonClasses(DecoratorMode $mode, string $variant): array
    {
        return match ($mode) {
            DecoratorMode::Tailwind => match ($variant) {
                'seed' => ['bg-amber-500', 'text-white', 'hover:bg-amber-600', 'focus:ring-amber-300'],
                default => ['border', 'border-slate-300', 'bg-white', 'text-slate-700', 'hover:bg-slate-50', 'focus:ring-indigo-300'],
            },
            DecoratorMode::Blade => match ($variant) {
                'seed' => ['fg-blade-btn-warning'],
                default => ['fg-blade-btn-outline'],
            },
            DecoratorMode::Twig => match ($variant) {
                'seed' => ['fg-twig-btn-warning'],
                default => ['fg-twig-btn-outline'],
            },
            default => match ($variant) {
                'seed' => ['btn-warning'],
                default => ['btn-outline-secondary'],
            },
        };
    }

    /**
     * @return list<string>
     */
    private function navLinkClasses(DecoratorMode $mode, bool $active): array
    {
        return match ($mode) {
            DecoratorMode::Tailwind => $active
                ? ['rounded-lg', 'bg-indigo-600', 'px-3', 'py-2', 'text-sm', 'font-semibold', 'text-white']
                : ['rounded-lg', 'px-3', 'py-2', 'text-sm', 'font-medium', 'text-slate-700', 'hover:bg-slate-100'],
            DecoratorMode::Blade => $active ? ['fg-blade-nav-link', 'active'] : ['fg-blade-nav-link'],
            DecoratorMode::Twig => $active ? ['fg-twig-nav-link', 'active'] : ['fg-twig-nav-link'],
            default => $active ? ['nav-link', 'active'] : ['nav-link'],
        };
    }
}
