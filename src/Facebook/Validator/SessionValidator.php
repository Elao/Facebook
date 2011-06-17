<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Validator;

use Facebook\Validator\SessionValidatorInterface;
use Facebook\Facebook;
use Facebook\Session;

/**
 * Cookie Loader tries to create a session from the cookie
 * works with the Cookie dumper
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class SessionValidator implements SessionValidatorInterface {

    protected $facebook;
    protected $sessionValidator;

    public function __construct(Facebook $facebook) {
        $this->facebook = $facebook;
    }

    /**
     * @see Facebook\Validator.SessionValidatorInterface::isValidSession()
     */
    public function isValidSession(array $session) {

        // make sure some essential fields exist
        if (is_array($session) && isset($session['uid']) && isset($session['access_token']) && isset($session['sig'])) {
            $session_without_sig = $session;
            unset($session_without_sig['sig']);
            $expected_sig = $this->generateSignature($session_without_sig, $this->facebook->getAppSecret());

            return $session['sig'] == $expected_sig;
        }
        return false;
    }

    protected function generateSignature($params, $secret) {
        // work with sorted data
        ksort($params);

        // generate the base string
        $base_string = '';
        foreach ($params as $key => $value) {
            $base_string .= $key . '=' . $value;
        }
        $base_string .= $secret;

        return md5($base_string);
    }

}