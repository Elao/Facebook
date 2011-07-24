<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Requester;

use Facebook\Requester\RequesterInterface;
use Facebook\Exception\ApiException;

/**
 * Curl Requester is the default requester to make api call
 * it uses Curl
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class CurlRequester implements RequesterInterface {

    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'facebook-php-3.0',
    );

    public function request($url, $params = array()) {
        foreach ($params as $key => $value) {
            if (!is_string($value)) {
                $params[$key] = json_encode($value);
            }
        }

        $ch = curl_init();

        $opts = self::$CURL_OPTS;
        $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        $opts[CURLOPT_URL] = $url;

        if (isset($opts[CURLOPT_HTTPHEADER])) {
            $existing_headers = $opts[CURLOPT_HTTPHEADER];
            $existing_headers[] = 'Expect:';
            $opts[CURLOPT_HTTPHEADER] = $existing_headers;
        } else {
            $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }

        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);

        if (curl_errno($ch) == 60) { // CURLE_SSL_CACERT
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/../Resources/fb_ca_chain_bundle.crt');
            $result = curl_exec($ch);
        }

        if ($result === false) {
            $e = new ApiException(array(
                        'error_code' => curl_errno($ch),
                        'error'      => array(
                        'message'    => curl_error($ch),
                        'type'       => 'CurlException',
                        )
                    ));
            curl_close($ch);
            throw $e;
        }
        curl_close($ch);

        if ($result) {
            $json_result = json_decode($result, true);
            if ($json_result) {
                return $json_result;
            }
            return $result;
        }
        return false;
    }

}
