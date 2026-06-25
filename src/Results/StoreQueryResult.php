<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class StoreQueryResult extends Result implements HttpResult
{

    public function getStores()
    {
        return $this->getData()['pois'] ?? [];
    }

}