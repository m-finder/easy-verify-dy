<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class CodeQueryByOrderIdResult extends Result implements HttpResult
{

    public function getCertificates()
    {
        return $this->getData()['certificates'] ?? [];
    }

}