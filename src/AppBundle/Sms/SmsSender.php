<?php

namespace AppBundle\Sms;

interface SmsSender
{
    public function send(Sms $sms);
}
