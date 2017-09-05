<?php
namespace juniorb2ss\LaravelExtendedValidationRules\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Validation\Rule;
use juniorb2ss\LaravelExtendedValidationRules\Exceptions\NotFoundMailgunPubKeyException;

class MailGunValidateEmailAddressRule implements Rule
{

    /**
     * GuzzgleHttp Client
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Mailgun Email Validation Key
     * @var string
     */
    protected $pubkey;

    /**
     * [$message description]
     * @var string
     */
    protected $message = 'Sorry, invalid email address.';

    /**
     * Mailgun API URL
     * @var string
     */
    protected $apiUrl = 'https://api.mailgun.net/v3/address/validate';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $config = [], ClientInterface $client = null)
    {
        /**
         * Instance new client to transport HTTP request
         * @var \GuzzleHttp\ClientInterface
         */
        $this->client = $client ?? new Client;

        /**
         * Get mailgun pubkey.
         * @var string
         */
        $pubkey = array_get($config, 'pubkey', env('MAILGUN_PUBKEY', null));

        // need a pubkey to make API request
        if ($pubkey === null) {
            throw new NotFoundMailgunPubKeyException;
        }

        /**
         * To make api calls, need mailgun email validation key
         * you can get this key in: https://app.mailgun.com/app/account/security
         * @var array
         */
        $this->pubkey = $pubkey;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $email
     * @return bool
     */
    public function passes($attribute, $email): bool
    {
        // Make request to mailgun api with address
        // and get response with validation email
        $this->mailgunApiValidade($email)
             ->validateMessage();

        return $this->isValidEmail();
    }

    /**
     * [mailgunApiValidade description]
     * @param  [type] $address [description]
     * @return [type]          [description]
     */
    protected function mailgunApiValidade($address): self
    {
        $url = $this->apiUrl;
        $pubKey = $this->pubkey;

        // Make request to API and get payload response
        $response = $this->client->request('GET', $url, [
            'query' => [
                'api_key' => $pubKey,
                'address' => $address
            ]
        ]);

        // decode bla bla bla
        $this->response = json_decode($response->getBody());

        return $this;
    }

    /**
     * [isValidEmail description]
     * @return boolean [description]
     */
    protected function isValidEmail(): bool
    {
        return $this->response->is_valid;
    }

    /**
     * [validateMessage description]
     * @return [type] [description]
     */
    protected function validateMessage()
    {
        if ($this->response->did_you_mean) {
            $didYouMean = $this->response->did_you_mean;

            $this->message = "{$this->message} Did you mean {$didYouMean}?";
        }
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}
