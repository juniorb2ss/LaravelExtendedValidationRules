<?php
namespace juniorb2ss\LaravelExtendedValidationRules\Tests;

use juniorb2ss\LaravelExtendedValidationRules\Tests\TestCase;
use juniorb2ss\LaravelExtendedValidationRules\Rules\ValidateEmailAddressRule;

class ValidateEmailAddressRuleTest extends TestCase
{
    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function testOnlyPaidAccounts()
    {
        $validator = new ValidateEmailAddressRule(
            [
                'pubkey' => 'something'
            ],
            $this->clientMock($this->getStubContent('onlyPaidAccounts.json'), function ($response) {
                return $response->withStatus(403);
            })
        );

        $validator->passes('', 'foo@bar.net');
    }

    /**
     * @expectedException \juniorb2ss\LaravelExtendedValidationRules\Exceptions\NotFoundMailgunPubKeyException
     */
    public function testNotFoundPubKey()
    {
        $validator = new ValidateEmailAddressRule(
            [],
            $this->clientMock($this->getStubContent('onlyPaidAccounts.json'), function ($response) {
                return $response->withStatus(403);
            })
        );

        $validator->passes('', 'foo@bar.net');
    }

    public function testInvalidEmail()
    {
        $validator = new ValidateEmailAddressRule(
            [
                'pubkey' => 'something'
            ],
            $this->clientMock($this->getStubContent('invalidEmail.json'))
        );

        $passes = $validator->passes('', 'foo@bar.net');

        $this->assertFalse($passes);
        $this->assertEquals('Sorry, invalid email address.', $validator->message());
    }

    public function testValidEmail()
    {
        $validator = new ValidateEmailAddressRule(
            [
                'pubkey' => 'something'
            ],
            $this->clientMock($this->getStubContent('validEmail.json'))
        );

        $passes = $validator->passes('', 'foo@bar.net');

        $this->assertTrue($passes);
    }

    public function testDidYouMeanEmail()
    {
        $validator = new ValidateEmailAddressRule(
            [
                'pubkey' => 'something'
            ],
            $this->clientMock($this->getStubContent('invalidEmailWithDidYouMean.json'))
        );

        $passes = $validator->passes('', 'foo@bar.net');

        $this->assertFalse($passes);
        $this->assertEquals('Sorry, invalid email address. Did you mean foo@bar.net?', $validator->message());
    }
}
