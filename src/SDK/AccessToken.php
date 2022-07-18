<?php

namespace xXutianyi\WecomAuth\SDK;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use xXutianyi\WecomAuth\Config;
use xXutianyi\WecomAuth\utils\HttpCall;

class AccessToken
{
    const ACCESS_TOKEN_URL = "/gettoken";

    const BASE_URL = "https://qyapi.weixin.qq.com/cgi-bin";

    private array $params;

    private FilesystemAdapter $cache;
    private CacheItem $AccessTokenInstance;

    /**
     * @param Config $config
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __construct(Config $config)
    {
        //初始化 cache 实例
        $this->cache = new FilesystemAdapter();
        $cacheKey = "xXutianyi.wecom.app.access_token." . $config->AgentID . "." . $config->CorpId;
        $this->AccessTokenInstance = $this->cache->getItem($cacheKey);

        //初始化配置
        $this->params = $config->GetAccessTokenParams();
        $this->GetTenantAccessToken();
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function GetTenantAccessToken(): string
    {
        $TenantAccessToken = $this->AccessTokenInstance->get();
        if ($TenantAccessToken) {
            return $TenantAccessToken;
        }
        return $this->RefreshTenantAccessToken();
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function RefreshTenantAccessToken(): string
    {
        $url = self::BASE_URL . self::ACCESS_TOKEN_URL;
        $call = HttpCall::get($url, $this->params);
        $call = json_decode($call, true);
        if (!$call['access_token']) {
            throw new \Exception("Get Access Token Error:" . json_encode($call));
        }
        $this->AccessTokenInstance->set($call['access_token']);
        $this->AccessTokenInstance->expiresAfter($call['expires_in']);
        $this->cache->save($this->AccessTokenInstance);
        return $this->AccessTokenInstance->get();
    }
}