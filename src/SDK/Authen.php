<?php

namespace xXutianyi\WecomAuth\SDK;

use JetBrains\PhpStorm\ArrayShape;
use xXutianyi\WecomAuth\SDK;

class Authen extends SDK
{
    const AUTHEN_BY_CODE = "/user/getuserinfo";
    const GET_USER_BY_TICKET = "/user/getuserdetail";

    const GET_QR_PARAMS = [
        "id" => "string",
        "appid" => "string",
        "agentid" => "string",
        "redirect_uri" => "string",
        "state" => "string",
        "href" => "string",
        "lang" => "string"
    ];

    public function GetBootUrl($redirectUrl, $state = ""): string
    {
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->config->CorpId}&redirect_uri=$redirectUrl&response_type=code&scope=snsapi_base&state=$state&agentid={$this->config->AgentID}#wechat_redirect";
    }

    public function GetBootUrlPrivate($redirectUrl, $state = ""): string
    {
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->config->CorpId}&redirect_uri=$redirectUrl&response_type=code&scope=snsapi_privateinfo&state=$state&agentid={$this->config->AgentID}#wechat_redirect";
    }

    #[ArrayShape(self::GET_QR_PARAMS)]
    public function GetQrParams(int|bool $redirectUrl, $htmlID, $state = "", $href = "", $lang = "zh"): array
    {
        return [
            "id" => $htmlID,
            "appid" => $this->config->CorpId,
            "agentid" => $this->config->AgentID,
            "redirect_uri" => urlencode($redirectUrl),
            "state" => $state,
            "href" => $href,
            "lang" => $lang,
        ];
    }

    public function AuthenByCode($code): array
    {
        $query = [
            'code' => $code
        ];
        return $this->httpGet(self::AUTHEN_BY_CODE, $query);
    }

    public function GetUserByTicket($ticket): array
    {
        $param = [
            'user_ticket' => $ticket
        ];

        return $this->httpPost(self::GET_USER_BY_TICKET, [], $param);

    }

}