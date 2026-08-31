<?php

namespace App\Services;

use libphonenumber\{PhoneNumberUtil, PhoneNumber, PhoneNumberFormat};


class PhoneNumberService
{
    public PhoneNumberUtil $phoneUtil;
    public function __construct(public String $phoneNumber)
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }
    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneUtil->parse($this->phoneNumber);
    }
    public function formattedNumber(): string
    {
        $phoneNumber = $this->getPhoneNumber();
        return $this->phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);
    }

  
    function isValid(): bool
    {
        try {
            $phoneNumber = $this->getPhoneNumber();
        } catch (\Throwable $e) {
            return false;
        }
        return $this->phoneUtil->isValidNumber($phoneNumber);
    }
}