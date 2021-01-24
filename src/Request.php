<?php

namespace HTTP_Request;

class Request
{
    private static $contentTypes = [
        'json' => 'Content-Type: application/json',
        'html' => 'Content-Type: text/html',
        'xml'  => 'Content-Type: text/xml'
    ];

    private static function makeRequest($url, $auth, $method, $data, $token, $isJson=true) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/../cacert.pem');
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
        curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if ($isJson == true) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        if (!is_null($auth)) {
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
        }
    
        switch ($method) {
            case 'get':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            default:
                curl_setopt($ch, CURLOPT_POST, true);
        }
        
        if (!is_null($data) && $data != '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    
        if (!is_null($token) && $token != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $token]);
        }

        $output = curl_exec($ch);
        if ($output !== false) {
            return json_decode($output, true);
        }
        return $output;
    }
    
    public static function get($url, $auth, $data=null, $token=null) {
        return self::makeRequest($url, $auth, 'get', $data, $token);
    }
    
    public static function post($url, $auth, $data=null, $token=null, $isJson=true) {
        return self::makeRequest($url, $auth, 'post', $data, $token, $isJson);
    }
    
    public static function put($url, $auth, $data=null, $token=null) {
        return self::makeRequest($url, $auth, 'put', $data, $token);
    }  
}