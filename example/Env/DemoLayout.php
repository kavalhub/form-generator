<?php
declare(strict_types=1);

namespace Kavalhub\Example\Env;

use Kavalhub\FormGenerator\Element\Interface\ElementInterface;
use Kavalhub\FormGenerator\Html\Button;
use Kavalhub\FormGenerator\Html\Group;
use Kavalhub\FormGenerator\Html\Label;
use Kavalhub\FormGenerator\Html\Link;

final class DemoLayout
{
    public const FORM_CARD_GROUP = 'demoFormCard';

    public static function header(DecoratorMode $mode): string
    {
        $group = (new Group('demoHeader'))->setPath('layout/Header');
        $group->addElement(
            (new Label('demoTitle'))->setLabel('<h1 class="demo-title">form-generator demo</h1>')->setAllowHtml()
        );
        $group->addElement(
            (new Label('demoSubtitle'))->setLabel(
                '<p class="demo-subtitle">Демонстрация пакета <code>kavalhub/form-generator</code> '
                . '(namespace <code>Kavalhub\\Example</code>)</p>'
            )->setAllowHtml()
        );

        return FormRenderer::html($group, self::shellMode($mode));
    }

    public static function toolbar(DecoratorMode $mode): string
    {
        $group = (new Group('demoToolbar'))->setPath('layout/Toolbar');
        $group->addElement(
            (new Button('seed'))
                ->setType('button')
                ->setId('btn-seed-data')
                ->setLabel('Добавить тестовые данные')
                ->addClass(self::buttonClasses($mode, 'seed'))
        );
        $group->addElement(
            (new Link('apiDocs', '/api-docs.html', 'Документация JSON API'))
                ->addClass(self::buttonClasses($mode, 'link'))
        );

        return FormRenderer::html($group, self::shellMode($mode));
    }

    /**
     * @param array<string, string> $items
     */
    public static function nav(string $activePage, DecoratorMode $mode, array $items): string
    {
        $group = (new Group('demoNav'))->setPath('layout/Nav');
        foreach ($items as $key => $label) {
            $params = array_merge(
                DemoSettingsForm::stateQueryParams($mode),
                ['page' => $key],
            );
            $link = (new Link('nav_' . $key, '?' . http_build_query($params), $label))
                ->addClass(self::navLinkClasses($mode, $key === $activePage));
            $group->addElement($link);
        }

        return FormRenderer::html($group, self::shellMode($mode));
    }

    public static function flash(string $message, DecoratorMode $mode): string
    {
        if ($message === '') {
            return '';
        }

        $group = (new Group('demoFlash'))->setPath('layout/Flash');
        $group->addElement((new Label('flashMessage'))->setLabel($message));

        return FormRenderer::html($group, self::shellMode($mode));
    }

    public static function settings(DecoratorMode $mode): string
    {
        return FormRenderer::html(new DemoSettingsForm($mode), self::shellMode($mode));
    }

    public static function formCardWrap(ElementInterface $form): Group
    {
        return self::ensureFormCardWrap($form);
    }

    public static function formCard(?ElementInterface $form, DecoratorMode $mode): string
    {
        if ($form === null) {
            return '';
        }

        $card = self::ensureFormCardWrap($form);
        if ($card->getPath() === '') {
            $card->setPath('layout/FormCard');
        }

        return FormRenderer::html($card, $mode);
    }

    private static function ensureFormCardWrap(ElementInterface $form): Group
    {
        $parent = $form->getParent();
        if ($parent instanceof Group && $parent->getName() === self::FORM_CARD_GROUP) {
            return $parent;
        }

        return (new Group(self::FORM_CARD_GROUP))->addElement($form);
    }

    private static function shellMode(DecoratorMode $mode): DecoratorMode
    {
        return $mode === DecoratorMode::Html ? DecoratorMode::Bootstrap : $mode;
    }

    /**
     * @return list<string>
     */
    private static function buttonClasses(DecoratorMode $mode, string $variant): array
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
    private static function navLinkClasses(DecoratorMode $mode, bool $active): array
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
