<?php

namespace xXutianyi\WecomAuth;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use xXutianyi\WecomAuth\SDK\AccessToken;
use xXutianyi\WecomAuth\utils\HttpCall;

abstract class SDK
{

    const BASE_URL = "https://qyapi.weixin.qq.com/cgi-bin";

    protected string $AccessToken;
    protected Config $config;

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $TenantAccessTokenInstance = new AccessToken($config);
        $this->AccessToken = $TenantAccessTokenInstance->GetTenantAccessToken();
    }

    /**
     * @throws Exception
     */
    protected function unpackResponse($raw): array
    {
        $raw = json_decode($raw, true);
        $this->checkError($raw);
        return $raw;
    }

    /**
     * @throws Exception
     */
    private function checkError($response)
    {
        $code = $response['errcode'];
        $msg = $response['errmsg'];
        if ($code) {
            $errorMsg = "Wecom Api Call Error: $msg($code)";
            throw new Exception($errorMsg, $code);
        }
    }

    /**
     * @param string $url
     * @return string
     */
    protected function makeUrl(string $url): string
    {
        return self::BASE_URL . $url;
    }

    protected function makeQuery(array $query): array
    {
        $query += [
            'access_token' => $this->AccessToken
        ];
        return $query;
    }

    /**
     * @param string $url
     * @param array $query
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    protected function httpGet(string $url, array $query = []): array
    {
        $res = HttpCall::get(
            $this->makeUrl($url),
            $this->makeQuery($query),
        );
        return $this->unpackResponse($res);
    }

    /**
     * @param string $url
     * @param array $query
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    protected function httpPost(string $url, array $query = [], array $params = [], array $form = []): array
    {
        $res = HttpCall::post(
            $this->makeUrl($url),
            $this->makeQuery($query),
            $params,
            $form,
        );
        return $this->unpackResponse($res);
    }

}