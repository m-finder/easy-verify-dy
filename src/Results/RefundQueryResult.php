<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class RefundQueryResult extends Result implements HttpResult
{

    public function getList()
    {
        return $this->getData()['after_sale_order_list'] ?? [];
    }

}