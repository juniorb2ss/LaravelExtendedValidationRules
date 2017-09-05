# Laravel 5.5 Rules Extends

## Validate Email With Mailgun Service
```php
    use juniorb2ss\LaravelExtendedValidationRules\Rules\MailGunValidateEmailAddressRule;

    return Validator::make($inputs, [
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'max:255',
            'unique:users',
            new MailGunValidateEmailAddressRule // to make validation in mailgun service
        ]
    ]);

```