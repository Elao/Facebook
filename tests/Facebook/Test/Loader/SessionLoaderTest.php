<?php
namespace Facebook\Test\Loader;

use Facebook\Session;

use Facebook\Loader\SessionLoader;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;

use Facebook\Loader\SignedRequestLoader;


class SessionLoaderTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testLoader()
    {
    	$session = new Session($this->facebook, array('access_token' => 'mytoken', 'uid' => 'myuid'));
    	
    	$loader = new SessionLoader($this->facebook);
    	$this->assertFalse($loader->support());
    	$_SESSION[$loader->getSessionKey()] = serialize($session->toArray());
    	$this->assertTrue($loader->support());
    	
    	$this->assertInstanceOf('Facebook\\Session', $loader->auth());
    }
}  