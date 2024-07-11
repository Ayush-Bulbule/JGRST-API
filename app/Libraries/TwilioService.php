<?php

namespace App\Libraries;

use Twilio\Rest\Client;

class TwilioService
{
    private $accountSid;
    private $authToken;
    private $twilioPhoneNumber;

    public function __construct()
    {
        $this->accountSid = 'YOUR ACC ID HERE';
        $this->authToken = 'TOKEN HERE';
        $this->twilioPhoneNumber = 'PHONE NO HERE';
    }

    public function sendSMS($to, $message)
    {
        $twilio = new Client($this->accountSid, $this->authToken);
        $twilio->messages->create($to, ['from' => $this->twilioPhoneNumber, 'body' => $message]);
    }
}
