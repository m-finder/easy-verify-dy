<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class ClientTokenResult extends Result implements HttpResult
{
    public function getAccessToken()
    {
        return $this->getData()['access_token'] ?? null;
    }

    public function getExpiresIn()
    {
        return $this->getData()['expires_in'] ?? null;
    }
}