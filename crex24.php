<?php
/**
 * Copyright (c) 2018. Nicolas Choukroun.
 * Copyright (c) 2018. The PHPSnipe Developers.
 * This program is free software; you can redistribute it and/or modify it under the terms of the Attribution 4.0 International License as published by the Creative Commons Corporation; either version 2 of the License, or (at your option) any later version.  See COPYING for more details.
 *
 */


/**
 * Description of crex24
 * Simple base class to access teh CREX24 API
 * @author Nicolas Choukroun
 */
class Crex24 {

    private $apiKey = '';
    private $apiSecret = '';
    
    public function __construct($key, $secret) {
        if ($key=="" || $secret=="") die("API Key and API Secret cannot be null.");
        $this->apiKey = $key;
        $this->apiSecret = $secret;
    }

    function public($type, $func = "", $filter = "") {
        if ($filter == "") {
            $command = 'https://api.crex24.com/v2/public/' . $type;
        } else {
            $command = 'https://api.crex24.com/v2/public/' . $type . '?' . $func . '=' . $filter;
        }
        //echo $command;exit;
        $get_data = $this->call('GET', $command, false);

        $response = json_decode($get_data, true);
        $errors = $response['response']['errors'];
        return $response;
    }

    function call($method = "GET", $url, $data) {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "GET":
                curl_setopt($curl, CURLOPT_GET, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'APIKEY: '.$this->apiKey,
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);

        if (!$result) {
            die("Connection Failure");
        }
        curl_close($curl);
        return $result;
    }

    function buy($volume, $price) {
        $path = '/v2/trading/placeOrder';
        $url = 'https://api.crex24.com';

        if ($this->apiKey=="" || $this->apiSecret=="") die("API KEY or API SECRET not defined.");
        $body = '{
        "instrument": "KYF-BTC",
        "side": "buy",
        "volume": ' . $volume . ',
        "price": ' . $this->strnbr($price) . '
        }';
        $jsonbody = json_encode($body);
        //echo $body . "\n";
        $nonce = round(microtime(true) * 1000);

        $key = base64_decode($this->apiSecret);
        $message = $url . $nonce . $body;
        //$signature = base64_encode(hash_hmac('sha512', $message, $apiKey, true));
        $signature = base64_encode(hash_hmac('sha512', $path . $nonce . $body, base64_decode($this->apiSecret), true));

        $curl = curl_init($url . $path);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Length:' . strlen($body),
            'X-CREX24-API-KEY:' . $this->apiKey,
            'X-CREX24-API-NONCE:' . $nonce,
            'X-CREX24-API-SIGN:' . $signature,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_SESSIONID_CACHE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; crex24 API client; ' . php_uname('s') . '; PHP/' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . ')');

        $responseBody = curl_exec($curl);
        $responseStatusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        return $responseBody;
    }

    function sell($volume, $price) {
        $path = '/v2/trading/placeOrder';
        $url = 'https://api.crex24.com';



        $body = '{
        "instrument": "KYF-BTC",
        "side": "sell",
        "volume": ' . $volume . ',
        "price": ' . $this->strnbr($price) . '
        }';

        $jsonbody = json_encode($body);
        //echo $body . "\n";
        $nonce = round(microtime(true) * 1000);

        $key = base64_decode($this->apiSecret);
        $message = $url . $nonce . $body;
        //$signature = base64_encode(hash_hmac('sha512', $message, $apiKey, true));
        $signature = base64_encode(hash_hmac('sha512', $path . $nonce . $body, base64_decode($this->apiSecret), true));

        $curl = curl_init($url . $path);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Length:' . strlen($body),
            'X-CREX24-API-KEY:' . $this->apiKey,
            'X-CREX24-API-NONCE:' . $nonce,
            'X-CREX24-API-SIGN:' . $signature,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_SESSIONID_CACHE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; crex24 API client; ' . php_uname('s') . '; PHP/' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . ')');

        $responseBody = curl_exec($curl);
        $responseStatusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        return $responseBody;
    }

    
    function getBuyWall($debug = false) {
        $buy = array();
        if ($debug)
            echo "Order Book Buy Wall\n";
        $r = $this->public("orderbook", "instrument", "KYF-BTC&limit=10");
        for ($i = 0; $i < 10; $i++) {
            $buy[$i]['price'] = $r['buyLevels'][$i]['price'];
            $buy[$i]['volume'] = $r['buyLevels'][$i]['volume'];
        }
        if ($debug) {
            for ($i = 0; $i < 10; $i++) {
                echo intval($i) . " - " . $this->strnbr($buy[$i]['price']) . " - " . $buy[$i]['volume'] . "\r\n";
            }
        }
        return $buy;
    }

    function getSellWall($debug = false) {
        $sell = array();
        if ($debug)
            echo "Order Book Sell Wall\n";
        $r = $this->public("orderbook", "instrument", "KYF-BTC&limit=10");
        for ($i = 0; $i < 10; $i++) {
            $sell[9 - $i]['price'] = $r['sellLevels'][$i]['price'];
            $sell[9 - $i]['volume'] = $r['sellLevels'][$i]['volume'];
        }
        if ($debug) {

            for ($i = 0; $i < 10; $i++) {
                echo intval($i) . " - " . $this->strnbr($sell[$i]['price']) . " - " . $sell[$i]['volume'] . "\r\n";
            }
        }
        return $sell;
    }

    function getInstruments($f) {
        $buy = array();
        $r = $this->public("instruments", "filter", $f);
        return $r;
    }

    function getPrice() {
        $r = $this->public("tickers", "instrument", "KYF-BTC");
        return $r[0]['last'];
    }

    function strnbr($kyf) {
        return number_format($kyf, 11);
    }

}
