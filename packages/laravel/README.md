# kavalhub/form-generator-laravel

Мост между [kavalhub/form-generator](https://github.com/kavalhub/form-generator) и Laravel Validation.

## Установка

```bash
composer require kavalhub/form-generator-laravel
```

## Компоненты

| Класс | Описание |
|-------|----------|
| `LaravelRequestAdapter` | `RequestInterface` поверх `Illuminate\Http\Request` |
| `LaravelElementValidator` | `ElementValidatorInterface` — core + правила Laravel |

## Пример

```php
use Kavalhub\FormGenerator\Laravel\LaravelElementValidator;
use Kavalhub\FormGenerator\Laravel\LaravelRequestAdapter;

$adapter = new LaravelRequestAdapter($request);
$validator = new LaravelElementValidator($adapter, app(\Illuminate\Validation\Factory::class));
$validator->setRules([
    'contact_email' => 'required|email|max:255',
]);

if ($validator->checkSubmit($submit) && $validator->handle($form)) {
    // ...
}
```

Правила используют **имена полей формы** (`getFormName()`), например `contact_email`, а не короткое `email`.
