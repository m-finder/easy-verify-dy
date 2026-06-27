<?php

namespace Results;


use Wu\EasyVerifyCommon\HttpResult;
use Wu\EasyVerifyDy\Results\Result;

class RefundApplyResult extends Result implements HttpResult
{

    public function getOrderId()
    {
        return $this->getData()['order_id'] ?? null;
    }

    public function isApplySuccess()
    {
        return $this->getData()['success'] ?? false;
    }

    public function getAfterSaleId()
    {
        return $this->getData()['after_sale_id'] ?? false;
    }

    public function getAfterSaleInfoList()
    {
        return $this->getData()['after_sale_info_list'] ?? false;
    }

}