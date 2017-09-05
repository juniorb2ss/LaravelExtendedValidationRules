# Laravel 5.5 Rules Extends

### Validate Email With Mailgun Service

In .env file you need define your api pub key
```env
MAILGUN_PUBKEY=pubkey-5ogiflzbnjrljiky49qxsiozqef5jxp7
```

To make validation:
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