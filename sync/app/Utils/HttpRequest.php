<?php

namespace App\Utils;

class HttpRequest
{

    public static function get($url)
    {

        showLog("GET:", $url);

        $curl = curl_init();

        $SSL = substr($url, 0, 8) == "https://" ? true : false;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        if ($SSL) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        // 如果开启了代理则使用代理
        if (defined('PROXY') && defined('PROXY_TYPE')) {
            curl_setopt($curl, CURLOPT_PROXY, PROXY);
            curl_setopt($curl, CURLOPT_PROXYTYPE, PROXY_TYPE);
        }

        $response = curl_exec($curl);

        showLog('HTTP CODE:', curl_getinfo($curl)['http_code']);

        curl_close($curl);

        return $response;
    }

    /**
     * TODO 待完善,现在还用不到post
     * @param $url
     * @param array $params
     * @return bool|string
     */
    public static function post($url, $params = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}