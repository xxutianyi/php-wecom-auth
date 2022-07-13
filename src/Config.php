<?php

namespace xXutianyi\WecomAuth;

use JetBrains\PhpStorm\ArrayShape;

class Config
{
    const GetAccessTokenParams = [
        'corpid' => "string",
        'corpsecret' => "string",
    ];

    public string $CorpId;
    public string $AgentID;
    private string $CorpSecret;

    public function __construct($CorpId, $CorpSecret, $AgentID = "")
    {
        $this->CorpId = $CorpId;
        $this->CorpSecret = $CorpSecret;
        $this->AgentID = $AgentID;
    }

    /**
     * @return array
     */
    #[ArrayShape(self::GetAccessTokenParams)]
    public function GetAccessTokenParams(): array
    {
        return [
            'corpid' => $this->CorpId,
            'corpsecret' => $this->CorpSecret,
        ];
    }
}