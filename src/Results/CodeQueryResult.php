<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class CodeQueryResult extends Result implements HttpResult
{

    public function getCertificate()
    {
        return $this->getData()['certificate'] ?? [];
    }

    public function getAmount()
    {
        return $this->getCertificate()['amount'] ?? [];
    }

    public function getStatus()
    {
        return $this->getCertificate()['status'] ?? null;
    }

    public function getSku()
    {
        return $this->getCertificate()['sku'] ?? [];
    }

    public function getSkuId()
    {
        return $this->getSku()['sku_id'] ?? null;
    }

    public function getSkuTitle()
    {
        return $this->getSku()['title'] ?? null;
    }

    public function getSkuGrouponType()
    {
        return $this->getSku()['groupon_type'] ?? null;
    }

    public function getTimeCard()
    {
        return $this->getCertificate()['time_card'] ?? [];
    }

    public function getVerify()
    {
        return $this->getCertificate()['verify'] ?? [];
    }

}