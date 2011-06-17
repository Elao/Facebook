<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Loader;

use Facebook\Loader\LoaderInterface;
use Facebook\Facebook;
use Facebook\Session;
use Facebook\Request\Request;

/**
 * Application Loader tries to create a session by retriving a Access Token
 * by calling the oauth api with app id and app secret
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class ApplicationLoader implements LoaderInterface {
    const APP_OAUTH_TOKEN_REQUEST_FORMAT = "https://graph.facebook.com/oauth/access_token";

    protected $facebook;
    protected $code;

    public function __construct(Facebook $facebook, $code = null) {
        $this->facebook = $facebook;
        $this->code = $code;
    }

    public function getCode() {
        return $this->code;
    }

    public function support() {
        return true;
    }

    public function auth() {

        $url = self::APP_OAUTH_TOKEN_REQUEST_FORMAT;
        $params = array(
            'client_id' => $this->facebook->getConfiguration()->getAppId(),
            'client_secret' => $this->facebook->getConfiguration()->getAppSecret(),
            'grant_type' => 'client_credentials'
        );


        if ($this->getCode()) {
            $params['code'] = $this->getCode();
        }

        $result = $this->facebook->getRequester()->request($url, $params);

        $accessTokenInfo = null;
        if (is_string($result)) {
            parse_str($result, $accessTokenInfo);
        }

        if ($accessTokenInfo && is_array($accessTokenInfo) && isset($accessTokenInfo['access_token'])) {
            return new Session($this->facebook, array('access_token' => $accessTokenInfo['access_token']));
        } else {
            return false;
        }
    }

}