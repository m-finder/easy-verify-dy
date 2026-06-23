<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class VerifyResult extends Result implements HttpResult
{
    public function isSuccess(): bool
    {
        return parent::isSuccess() && $this->getVerifyResults()[0]['result'] === 0;
    }

    public function isFail(): bool
    {
        return parent::isFail() || $this->getVerifyResults()[0]['result'] !== 0;
    }

    public function getVerifyResults()
    {
        return $this->getData()['verify_results'] ?? [];
    }

    public function getReason()
    {
        return $this->getVerifyResults()[0]['msg'] ?? parent::getReason();
    }

    public function getCertificateId()
    {
        return $this->getVerifyResults()[0]['certificate_id'] ?? null;
    }

    public function getOriginCode()
    {
        return $this->getVerifyResults()[0]['origin_code'] ?? null;
    }

    public function getVerifyId()
    {
        return $this->getVerifyResults()[0]['verify_id'] ?? null;
    }

    public function getOrderId()
    {
        return $this->getVerifyResults()[0]['order_id'] ?? null;
    }

    public function getCode()
    {
        return $this->getVerifyResults()[0]['code'] ?? null;
    }

    public function getAccountId()
    {
        return $this->getVerifyResults()[0]['account_id'] ?? null;
    }
}