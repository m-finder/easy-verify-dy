<p align="center"><img src="https://m-finder.github.io/images/avatar.jpeg"></p>
<p align="center">
<img src="https://img.shields.io/badge/Author-m--finder-red">
<img src="https://img.shields.io/badge/Laravel->=10.0-red">
<img src="https://img.shields.io/badge/PHP->=8.0-red">
</p>

# easy verify 抖音模块.

#### 接口清单
* [验券准备-二维码短链换 object_id](https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.prepare)
* [验券准备-encrypted_data (object_id)](https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.prepare)
* [验券准备-code](https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.prepare)
* [验券](https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.verify)
* [撤销核销](https://developer.open-douyin.com/docs/resource/zh-CN/local-life/develop/OpenAPI/general-capabilities/life.capacity.fulfilment/certificate.cancel)

#### 使用示例
* 发布并配置
```shell
php artisan vendor:publish --provider="Wu\EasyVerifyDy\Providers\ServiceProvider"
```
* 验券准备-短链换 object_id
```php
public function test_parsing()
{
    $string = new Application()->parsing("https://v.douyin.com/iQsX2aWL/");
    echo '二维码短链提取 object_id:', $string, PHP_EOL;
}
```
* 验券准备-encrypted_data (object_id)
```php
public function test_prepare_by_encrypt_data()
{
    $objectId = 'I08zVldiM1FKVU1Pc29uZmdoNTUxWGpCSExPVDJJVHJjTXZaakJkOEhEQWZ4enJSdlRZcFZPcWt3UDJ5WW1HMXVDUlVMVGJidFk1ajVzdUZMYndQYXVmQWxHaEV5OEl4MllsTFhOcnJvaHloY3dKUGZiS21iUjdoaU1WTC9uWjdKSFZWKytVQXNMRU1acTR1ZytiSUhRYStWL0YraDd5dVB4MlRqUHNnckxYeEZ4Zkt5am5qaEcvQ0lLZUJ0VnI1SXQrUGpyR0l2YUorbQ';
    $res = new Application($this->token)->prepare($objectId);
    echo $res->getLogId(), PHP_EOL;
    echo $res->getOrderId(), PHP_EOL;
    echo $res->getVerifyToken(), PHP_EOL;
}
```

* 验券准备-code
```php
public function test_prepare_by_code()
{
    $code = '121638498862402';
    $res = new Application($this->token)->prepareByCode($code);
    echo $res->getLogId(), PHP_EOL;
    echo $res->getOrderId(), PHP_EOL;
    echo $res->getVerifyToken(), PHP_EOL;
}
```
* 验券
```php
public function test_verify()
{
    $verifyToken = '563ac447-7368-431d-b38e-6f8c3e75109a';
    $encryptCode = 'CgYIASAHKAESLgosEBc7a1+TmVbom+Ro0XaTbSWcUCmqJmauIlqsas2IRXQQqRGOMEqxpBGz/KUaAA==';
    $storeId = '6726901086101276675';
    $res = new Application($this->token)->verify($verifyToken, [$encryptCode], $storeId);
    echo $res->getReason(), PHP_EOL;
    echo $res->getAccountId(), PHP_EOL;
    echo $res->getOrderId(), PHP_EOL;
    echo $res->getCertificateId(), PHP_EOL;
    echo $res->getVerifyId(), PHP_EOL;
    echo $res->getOriginCode(), PHP_EOL;
}
```

* 撤销核销
```php
public function test_cancel()
{
    $certId = '7513089961583886362';
    $verifyId = '7513110825529133096';

    $res = new Application($this->token)->cancel($certId, $verifyId);
    var_dump($res->getCancelResults());
    echo $res->getReason(), PHP_EOL;
}
```