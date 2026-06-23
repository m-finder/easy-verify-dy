<?php

namespace Wu\EasyVerifyDy;

use Illuminate\Support\Facades\Http;
use Wu\EasyVerifyDy\Results\CancelResult;
use Wu\EasyVerifyDy\Results\PrepareResult;
use Wu\EasyVerifyDy\Results\VerifyResult;

class Application
{
    protected const string URL = 'https://open.douyin.com';

    protected string $clientKey = '';
    protected string $clientSecret = '';

    public function __construct(
        protected $token = ''
    )
    {
        $this->clientKey = config('verify.dy.client_key');
        $this->clientSecret = config('verify.dy.client_secret');

        if (empty($this->clientKey || empty($this->clientSecret))) {
            abort(500, '抖音配置有误');
        }
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 验券准备-二维码短链提取 object_id
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.prepare
     * @param string $url
     * @return string
     */
    public function parsing(string $url): string
    {
        try {
            $res = Http::withoutRedirecting()->get($url);
            parse_str(parse_url($res->getHeader('Location')[0])['query'], $params);
            return $params['object_id'];
        } catch (\Exception $e) {
            info('抖音参数解析失败: ', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
            abort(500, '抖音参数解析失败');
        }
    }

    /**
     * 验券准备
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.prepare
     * @param string $data
     * @return PrepareResult
     */
    public function prepare(string $data): PrepareResult
    {
        validator([
            'data' => $data,
            'token' => $this->token,
        ], [
            'data' => 'required|string',
            'token' => 'required|string'
        ]);
        $url = '/goodlife/v1/fulfilment/certificate/prepare/';
        $params = [
            'encrypted_data' => $data
        ];
        return new PrepareResult($this->request($url, $params));
    }

    /**
     * 验券准备
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.prepare
     * @param string $code
     * @return PrepareResult
     */
    public function prepareByCode(string $code): PrepareResult
    {
        validator([
            'code' => filter_special($code),
            'token' => $this->token,
        ], [
            'code' => 'required|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/fulfilment/certificate/prepare/';

        $params = [
            'code' => $code
        ];

        return new PrepareResult($this->request($url, $params));
    }


    /**
     * 验券
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.verify
     * @param string $verifyToken
     * @param array $codes
     * @param string $storeId
     * @return VerifyResult
     */
    public function verify(string $verifyToken, array $codes, string $storeId): VerifyResult
    {
        validator([
            'verify_token' => $verifyToken,
            'encrypted_codes' => $codes,
            'store_id' => $storeId,
            'token' => $this->token,
        ], [
            'verify_token' => 'required|string',
            'encrypted_codes' => 'required|array',
            'store_id' => 'required|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/fulfilment/certificate/verify';

        $params = [
            'verify_token' => $verifyToken,
            'poi_id' => $storeId,
            'encrypted_codes' => $codes,
        ];

        return new VerifyResult($this->request($url, $params, 'post'));
    }

    /**
     * 取消核销
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.cancel
     * @param string $certificateId
     * @param string $verifyId
     * @param string $accountId
     * @return CancelResult
     */
    public function cancel(string $certificateId, string $verifyId, string $accountId = ''): CancelResult
    {

        validator([
            'certificate_id' => $certificateId,
            'verify_id' => $verifyId,
            'account_id' => $accountId,
            'token' => $this->token,
        ], [
            'certificate_id' => 'required|string',
            'verify_id' => 'required|array',
            'account_id' => 'nullable|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/fulfilment/certificate/cancel';

        $params = [
            'certificate_id' => $certificateId,
            'verify_id' => $verifyId,
            'account_id' => $accountId,
        ];

        return new CancelResult($this->request($url, $params, 'post'));
    }

    /**
     * 接口请求
     * @param string $url
     * @param array $params
     * @param string $method
     * @return array
     */
    protected function request(string $url, array $params, string $method = 'get'): array
    {
        try {

            $options = [];
            if (strtolower($method) === 'post') {
                $options['json'] = $params;
            } else {
                $options['query'] = $params;
            }

            $res = Http::withHeaders([
                'access-token' => $this->token,
                'content-type' => 'application/json'
            ])->send($method, self::URL . $url, $options);


            if ($res->failed()) {
                abort(500, '抖音接口请求失败');
            }

            $result = json_decode($res->getBody()->getContents(), true);

            if (config('verify.dy.log')) {
                info('抖音接口日志:', [
                    'url' => $url,
                    'params' => $params,
                    'res' => $result
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            info('抖音参数解析失败: ', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
            abort(500, $e->getMessage() ?? '抖音参数解析失败');
        }

    }
}