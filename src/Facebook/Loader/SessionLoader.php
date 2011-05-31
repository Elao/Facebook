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
 * Session Loader tries to create a facebook session from the PHP session
 * works with the Session Dumper
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class SessionLoader implements LoaderInterface
{
	protected $facebook;
	protected $session_key;
	
	public function __construct(Facebook $facebook, $session_key = 'fb_session') {
		$this->facebook 		 = $facebook;
		$this->session_key		 = $session_key;
	}
	
	public function getSessionKey()
	{
		return $this->session_key;
	}
	
	public function support() {
		return isset($_SESSION[$this->getSessionKey()]);
	}
		
	public function auth() {
		$this->facebook->debug("Loader Session Attempt");
		
		$session = @unserialize($_SESSION[$this->getSessionKey()]);
		if ($session && is_array($session))
		{
			return new Session($this->facebook, $session);
		}else{
			return false;
		}
	}
}