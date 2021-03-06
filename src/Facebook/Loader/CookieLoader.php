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
use Facebook\Validator\SessionValidatorInterface;

/**
 * Cookie Loader tries to create a session from the cookie
 * works with the Cookie dumper
 *
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class CookieLoader implements LoaderInterface {

    protected $facebook;
    protected $sessionCookieName;
    protected $sessionValidator;

    public function __construct(Facebook $facebook, SessionValidatorInterface $validator) {
        $this->facebook             = $facebook;
        $this->sessionValidator     = $validator;
    }

    public function getSessionCookieName() {
        return 'fbs_' . $this->facebook->getConfiguration()->getAppId();
    }
    
    public function support() {
        return isset($_COOKIE[$this->getSessionCookieName()]);
    }

    public function auth() {
        $session = array();
        parse_str(trim(get_magic_quotes_gpc() ? stripslashes($_COOKIE[$this->getSessionCookieName()]) : $_COOKIE[$this->getSessionCookieName()], '"'), $session);

        // We should validated session here buddy
        if ($this->getValidator()->isValidSession($session)) {
            return new Session($this->facebook, $session);
        } else {
            return false;
        }
    }

    public function getValidator()
    {

        return $this->sessionValidator;
    }
}