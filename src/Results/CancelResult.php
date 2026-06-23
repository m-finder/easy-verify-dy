<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class CancelResult extends Result implements HttpResult
{
    public function getCancelResults()
    {
        return $this->getData()['cancel_results'] ?? [];
    }

    public function getReason()
    {
        return $this->response['extra']['sub_description'] ?? parent::getReason();
    }
}