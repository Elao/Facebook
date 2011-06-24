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
use Facebook\Exception\AuthException;

/**
 * Signed Request Loader tries to create a session from the signed_request parameter
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class SignedRequestLoader implements LoaderInterface {

    protected $facebook;

    public function __construct(Facebook $facebook) {
        $this->facebook = $facebook;
    }

    public function support() {
        return isset($_REQUEST['signed_request']);
    }

    public function auth() {
        try {
            $signedRequest = $this->facebook->parseSignedRequest($_REQUEST['signed_request']);
        } catch (AuthException $e) {
            return false;
        }

        if ($signedRequest) {
            $session = $this->createSessionFromSignedRequest($signedRequest);
            return new Session($this->facebook, $session);
        }
        return false;
    }

    /**
     * Returns something that looks like our JS session object from the
     * signed token's data
     *
     * TODO: Nuke this once the login flow uses OLoader2
     *
     * @param Array the output of getSignedRequest
     * @return Array Something that will work as a session
     */
    protected function createSessionFromSignedRequest($data) {
        if (!isset($data['oauth_token'])) {
            return null;
        }

        $session = array(
            'uid'           => $data['user_id'],
            'access_token'  => $data['oauth_token'],
            'expires'       => $data['expires']
        );

        return $session;
    }

}