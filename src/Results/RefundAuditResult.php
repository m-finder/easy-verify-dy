<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class RefundAuditResult extends Result implements HttpResult
{

    public function isReentry()
    {
        return $this->getData()['reentry'] ?? false;
    }

}