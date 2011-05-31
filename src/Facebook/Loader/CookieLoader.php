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

/**
 * Cookie Loader tries to create a session from the cookie
 * works with the Cookie dumper
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class CookieLoader implements LoaderInterface
{
	protected $facebook;
	protected $sessionCookieName;
	
	public function __construct(Facebook $facebook) {
		$this->facebook 		 = $facebook;
		$this->sessionCookieName = 'fbs_'.$this->facebook->getConfiguration()->getAppId();
	}
	
	public function support() {
		return isset($_COOKIE[$this->sessionCookieName]);
	}
		
	public function auth() {
		$session = array();
		parse_str(trim(get_magic_quotes_gpc() ? stripslashes($_COOKIE[$this->sessionCookieName]) : $_COOKIE[$this->sessionCookieName], '"'), $session);
		
		// We should validated session here buddy
		
		
		return new Session($this->facebook, $session);
	}
}