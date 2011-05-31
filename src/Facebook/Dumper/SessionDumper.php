<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Dumper;

use Facebook\Dumper\DumperInterface;
use Facebook\Session;
use Facebook\Facebook;

/**
 * Session Dumper dump a Session Object into a the session
 *
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class SessionDumper implements DumperInterface 
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
	
	public function dump(Session $session) {
		$_SESSION[$this->getSessionKey()] = serialize($session->toArray());
		return true;
	}
	
	public function breakOnSucess() {
		return true;
	}
}