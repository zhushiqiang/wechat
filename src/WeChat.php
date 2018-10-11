<?php

namespace Shiqiang\WeChat;

use GuzzleHttp\Client;

class WeChat
{
    protected $suite_id;
    protected $suite_secret;
    protected $suit_token;
    protected $pre_auth_code;
    protected $guzzleOptions = [];

    /**
     * WeChat constructor.
     * @param string $suite_id
     * @param string $suite_secret
     */
    public function __construct(string $suite_id ,string $suite_secret)
    {
        $this->suite_id = $suite_id;
    }

    /**
     * @return Client
     */
	public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }
    /**
     * @param array $options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param string $suite_ticket
     * @return $this
     */
    public function getSuiteToken(string $suite_ticket)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token';

        $query = json_encode(array_filter([
            'suite_id' => $this->suite_id,
            'suite_secret' => $this->suite_secret,
            'suite_ticket' => $suite_ticket,
        ]));

        $this->suit_token = json_decode($this->getHttpClient()->post($url, [
            'body' => $query,
        ])->getBody()->getContents(),true)['suite_access_token'];

        return $this;
    }

    /**
     * @return $this
     */
    public function getPreAuthCode()
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_pre_auth_code';

        $query = array_filter([
            'suite_access_token' => $this->suit_token,
        ]);

        $this->pre_auth_code = json_decode($this->getHttpClient()->get($url, [
            'query' => $query,
        ])->getBody()->getContents(),true)['pre_auth_code'];

        return $this;
    }

    /**
     * @param string $redirect_uri
     * @param string $state
     * @return string
     */
    public function getUrl(string $redirect_uri,string $state)
    {
        $url = 'https://open.work.weixin.qq.com/3rdapp/install';
        $param = array(
            'suite_id' => $this->suite_id,
            'pre_auth_code' => $this->pre_auth_code,
            'redirect_uri' => $redirect_uri,
            'state' => $state,
        );
        $url .= "?".http_build_query($param);
        return $url;
    }
}