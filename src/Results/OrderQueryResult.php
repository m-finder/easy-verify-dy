<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class OrderQueryResult extends Result implements HttpResult
{

    public function getOrders()
    {
        return $this->getData()['orders'] ?? [];
    }

}