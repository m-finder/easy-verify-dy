<?php

namespace Wu\EasyVerifyDy;

use Illuminate\Support\Facades\Http;
use Wu\EasyVerifyDy\Results\CancelResult;
use Wu\EasyVerifyDy\Results\ClientTokenResult;
use Wu\EasyVerifyDy\Results\CodeQueryByOrderIdResult;
use Wu\EasyVerifyDy\Results\OrderQueryResult;
use Wu\EasyVerifyDy\Results\PrepareResult;
use Wu\EasyVerifyDy\Results\CodeQueryResult;
use Wu\EasyVerifyDy\Results\StoreQueryResult;
use Wu\EasyVerifyDy\Results\VerifyResult;

class Application
{
    protected const string URL = 'https://open.douyin.com';

    protected string $clientKey = '';
    protected string $clientSecret = '';

    public function __construct(protected $token = ''){
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
     * 生成 client-token
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/preparation/client_token
     * @return ClientTokenResult
     */
    public function getClientToken(): ClientTokenResult
    {
        $url = '/oauth/client_token';
        $params = [
            'client_key' => $this->clientKey,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credential'
        ];
        return new ClientTokenResult($this->request($url, $params));
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
     * 撤销核销
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
     * 撤销核销-批量撤销次卡订单下的一批验券记录
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.cancel
     * @param array $list
     * @param string $accountId
     * @return CancelResult
     */
    public function cancelBatch(array $list, string $accountId = ''): CancelResult
    {

        validator([
            'batch_cancel_info' => $list,
            'account_id' => $accountId,
            'token' => $this->token,
        ], [
            'batch_cancel_info' => 'required|array',
            'batch_cancel_info.order_id' => 'required|string',
            'batch_cancel_info.*.verify_id_list' => 'required|array|min:1',
            'account_id' => 'nullable|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/fulfilment/certificate/cancel';

        $params = [
            'certificate_id' => '0',
            'verify_id' => '0',
            'account_id' => $accountId,
            'batch_cancel_info' => $list
        ];

        return new CancelResult($this->request($url, $params, 'post'));
    }

    /**
     * 券状态查询
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.get
     * @param string $code
     * @param string $accountId
     * @return CodeQueryResult
     */
    public function codeQuery(string $code, string $accountId = ''): CodeQueryResult
    {

        validator([
            'code' => $code,
            'account_id' => $accountId,
            'token' => $this->token,
        ], [
            'code' => 'required|string',
            'account_id' => 'nullable|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/fulfilment/certificate/get';

        $params = [
            'encrypted_code' => $code,
            'account_id' => $accountId,
        ];

        return new CodeQueryResult($this->request($url, $params));
    }

    /**
     * 券状态批量查询
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.query
     * @param string $orderId
     * @param string $accountId
     * @return CodeQueryByOrderIdResult
     */
    public function codeQueryByOrderId(string $orderId, string $accountId = ''): CodeQueryByOrderIdResult
    {

        validator([
            'order_id' => $orderId,
            'account_id' => $accountId,
            'token' => $this->token,
        ], [
            'code' => 'required|string',
            'account_id' => 'nullable|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/fulfilment/certificate/query';

        $params = [
            'order_id' => $orderId,
            'account_id' => $accountId,
        ];

        return new CodeQueryByOrderIdResult($this->request($url, $params));
    }

    /**
     * 订单查询
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/order.query/query
     * @param array $params
     * @return OrderQueryResult
     */
    public function orderQuery(array $params): OrderQueryResult
    {

        validator([
            'page_num' => $params['page_num'],
            'page_size' => $params['page_size'],
            'create_order_end_time' => $params['create_order_end_time'] ?? null,
            'create_order_start_time' => $params['create_order_start_time'] ?? null,
            'cursor' => $params['cursor'] ?? null,
            'ext_order_id' => $params['ext_order_id'] ?? null,
            'get_secret_number' => $params['get_secret_number'] ?? null,
            'open_id' => $params['open_id'] ?? null,
            'order_id' => $params['order_id'] ?? null,
            'order_status' => $params['order_status'] ?? null,
            'update_order_end_time' => $params['update_order_end_time'] ?? null,
            'update_order_start_time' => $params['update_order_start_time'] ?? null,
            'account_id' => $params['account_id'] ?? null,
            'token' => $this->token,
        ], [
            'page_num' => 'required|num|min:1|max:10000',
            'page_size' => 'required|num|min:1',
            'create_order_end_time' => 'nullable|num',
            'create_order_start_time' => 'nullable|num',
            'cursor' => 'nullable|array',
            'ext_order_id' => 'nullable|string',
            'get_secret_number' => 'nullable|boolean',
            'open_id' => 'nullable|string',
            'order_id' => 'nullable|string',
            'order_status' => 'nullable|num',
            'update_order_end_time' => 'nullable|num',
            'update_order_start_time' => 'nullable|num',
            'account_id' => 'required|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/trade/order/query';
        return new OrderQueryResult($this->request($url, $params));
    }

    /**
     * 查询门店信息
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.shop/store-management/shop.query
     * @param array $params
     * @return StoreQueryResult
     */
    public function storeQuery(array $params): StoreQueryResult
    {

        validator([
            'page' => $params['page_num'],
            'size' => $params['page_size'],
            'poi_id' => $params['poi_id'] ?? null,
            'relation_type' => $params['relation_type'] ?? null,
            'third_id' => $params['third_id'] ?? null,
            'account_id' => $params['account_id'] ?? null,
            'token' => $this->token,
        ], [
            'page' => 'required|num|min:1|max:10000',
            'size' => 'required|num|min:1|max:50',
            'poi_id' => 'nullable|num',
            'relation_type' => 'nullable|num|in:0,1,2',
            'third_id' => 'nullable|array',
            'account_id' => 'required|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/shop/poi/query';
        return new StoreQueryResult($this->request($url, $params));
    }

    /**
     * 售后单详情查询
     * https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/groupon-refund/after-sale-order-detail
     * @param string $orderId
     * @param string $accountId
     * @param string $certificateId
     * @return StoreQueryResult
     */
    public function refundQuery(string $orderId, string $accountId, string $certificateId = ''): StoreQueryResult
    {

        validator([
            'order_id' => $orderId,
            'certificate_id' => $accountId,
            'account_id' => $accountId,
            'token' => $this->token,
        ], [
            'order_id' => 'required|string',
            'account_id' => 'required|string',
            'certificate_id' => 'nullable|string',
            'token' => 'required|string',
        ]);

        $url = '/goodlife/v1/akte/after_sale/order_detail/get';
        return new StoreQueryResult($this->request($url, $params));
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

            if($result['data']['error_code'] === 2190008){
                abort(403, $result['data']['description']);
            }

            return $result;
        } catch (\Exception $e) {
            info('抖音接口出错: ', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
            abort($e->getCode(), $e->getMessage() ?? '抖音接口出错');
        }

    }
}
