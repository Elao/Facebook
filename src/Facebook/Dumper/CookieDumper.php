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


use Facebook\Facebook;
use Facebook\Dumper\DumperInterface;
use Facebook\Session;

/**
 * Cookie Dumper dump a Session Object into a cookie
 *
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class CookieDumper implements DumperInterface 
{
	public function __construct(Facebook $facebook) {
		$this->facebook 		 = $facebook;
		$this->sessionCookieName = 'fbs_'.$this->facebook->getConfiguration()->getAppId();
	}
	
	public function dump(Session $session){
		$session = $session->toArray();
		
	    $cookieName = $this->sessionCookieName;
    	$value = 'deleted';
    	$expires = time() - 3600;
    	
	    $value   = '"' . http_build_query($session, null, '&') . '"';
		$expires = isset($session['expires']) ? $session['expires'] : null;

		
		setcookie($cookieName, $value, $expires, '/');
		
	}
	
	public function breakOnSucess() {
		return true;
	}
}