<?php

namespace Results;


use Wu\EasyVerifyCommon\HttpResult;
use Wu\EasyVerifyDy\Results\Result;

class RefundAuditResult extends Result implements HttpResult
{

    public function isReentry()
    {
        return $this->getData()['reentry'] ?? false;
    }

}