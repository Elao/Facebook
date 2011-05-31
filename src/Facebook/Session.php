<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook;

use Facebook\Object\FacebookObject;
use Facebook\Exception\ApiException;

/**
 * Facebook Session objects contains session related configuration
 * like access_token or uid
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class Session
{
	// The facebook instance
	protected $facebook;
	
	// The Access Token
	protected $access_token;
	
	// The User Facebook Id
	protected $uid;
	
	// Expiration
	protected $expires;
	
	// Other data return by the creation process other than access_token, uid or expires
	protected $data;
		
	protected $user;
	
    public function __construct(Facebook $facebook, $data) {
    	$this->user     = null;
		$this->facebook = $facebook;
	  	if (is_array($data)) {
			$this->initFromArray($data);	  		
	  	}
  	}
  	
  	/**
  	 * Initialize the session object from the data array
  	 * @param array $data
  	 * @return none
  	 */
  	public function initFromArray($data) {
  		if (isset($data['access_token'])) {
			$this->access_token = $data['access_token'];
		}
		
  		if (isset($data['uid'])) {
			$this->uid = $data['uid'];
		}
		
		if (isset($data['expires'])) {
			$this->expires = $data['expires'];
		}
		
		$this->data = $data;
  	}
  	
  	public function getAccessToken(){
  		return $this->access_token;
  	}
  
  	public function getUid() {
  		return $this->uid;
  	}
  	
  	public function getExpires() {
  		return $this->expires;
  	}
  	
  	public function getData() {
  		return $this->data;
  	}

  	/**
  	 * Return the User Object related to this session
  	 * @return \Facebook\Object\User
  	 */
  	public function getUser() {
  		if (!$this->user && $this->getUid()){
  			$this->user = new FacebookObject($this->facebook, array('id' => $this->getUid()));
  		}
  		return $this->user;
  	}
  
  	/**
  	 * Return true if the session has an associated user
  	 * @return Boolean
  	 */
  	public function hasUser() {
  		if ($this->getUser()) {
  			return true;
  		}
  		return false;
  	}
  	
  	/**
  	 * Dump the session as a array. Keys are ordered.
  	 * @return array
  	 */
  	public function toArray() {
  		$data = $this->getData();
  		ksort($data);
  		return $data;
  	}
}
