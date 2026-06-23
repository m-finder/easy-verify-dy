<?php

namespace Wu\EasyVerifyDy\Results;


use Wu\EasyVerifyCommon\HttpResult;

class Result implements HttpResult
{
    public function __construct(
        private $response = []
    ){

    }

    public function isSuccess(): bool
    {
        return $this->getData()['error_code'] === 0;
    }

    public function isFail(): bool
    {
        return $this->getData()['error_code'] !== 0;
    }

    public function isProcessing(): false
    {
        return false;
    }

    public function getData()
    {
        return $this->response['data'] ?? [];
    }

    public function getReason()
    {
        return $this->getData()['description'] ?? '';
    }

    public function getLogId()
    {
        return $this->response['extra']['logid'] ?? null;
    }
}