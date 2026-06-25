<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class PrepareResult extends Result implements HttpResult
{

    public function getCertificates()
    {
        return $this->getData()['certificates'] ?? [];
    }

    public function getOrderId()
    {
        return $this->getData()['order_id'] ?? null;
    }

    public function getVerifyToken()
    {
        return $this->getData()['verify_token'] ?? null;
    }

    public function getAmount()
    {
        return $this->getCertificates()[0]['amount'] ?? [];
    }

    public function getOriginalAmount()
    {
        return $this->getAmount()['original_amount'] ?? null;
    }

    public function getCouponAmount()
    {
        return $this->getAmount()['coupon_pay_amount'] ?? null;
    }

    public function getPayAmount()
    {
        return $this->getAmount()['pay_amount'] ?? null;
    }

    public function getSku()
    {
        return $this->getCertificates()[0]['sku'] ?? [];
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

    public function getAccountId()
    {
        return $this->getSku()['account_id'] ?? null;
    }

    public function getEncryptCode()
    {
        return $this->getCertificates()[0]['encrypted_code'] ?? null;
    }
}