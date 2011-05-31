<?php
namespace Facebook\Test;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;
use Facebook\Session;

use Facebook\Object\FacebookObject;

class SessionTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testCreation()
    {
    	$data = array(
    		'access_token' => 'plop',
    		'uid'		   => '123',
    		'sig'		   => 'test',
    		'expires'	   => '12345',
    		'others'	   => 'blabla'
		);
		
    	$session = new Session($this->facebook, $data);
		
    	$this->assertInternalType('array', 		   $session->toArray(), "Session to array should return an... array ;)");
    	$this->assertEquals($data, 				   $session->toArray(), "session to array must return the same array");
    	$this->assertEquals($data['access_token'], $session->getAccessToken());
    	$this->assertEquals($data['uid'], 	  	   $session->getUid());
    	$this->assertEquals($data['expires'], 	   $session->getExpires());
    }
    
    public function testUser()
    {
    	// With uid, we should have a user
    	$session = new Session($this->facebook, array('uid' => '123'));
    	$this->assertTrue($session->hasUser(), "Session should have a user");
		$this->assertInstanceOf('Facebook\Object\FacebookObject', $session->getUser(), "Session getUser should return a FacebookObject instance");

		$session = new Session($this->facebook, array('access_token' => 'plop'));
		$this->assertFalse($session->hasUser(), "Session shouldnt have a user");
		$this->assertNull($session->getUser(), "Session getUser should return null");
    }
}