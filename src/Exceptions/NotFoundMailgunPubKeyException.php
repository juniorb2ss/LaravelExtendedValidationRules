<?php
namespace juniorb2ss\LaravelExtendedValidationRules\Exceptions;

use Exception;

class NotFoundMailgunPubKeyException extends Exception
{
    public $message = 'Mailgun PubKey not configured.';
}
