<?php
namespace Facebook\Test\Loader;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;

use Facebook\Loader\SignedRequestLoader;


class SignedRequestTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testLoader()
    {
    	$signedRequestLoader = new SignedRequestLoader($this->facebook);
    	
    	// Test support()
    	$this->assertFalse($signedRequestLoader->support());
		$_REQUEST['signed_request'] = self::$VALID_SIGNED_REQUEST;
		$this->assertTrue($signedRequestLoader->support());
    	
    	// Test auth
		$this->assertInstanceOf('\Facebook\Session', $signedRequestLoader->auth());
	
		$_REQUEST['signed_request'] = self::$INVALID_SIGNED_REQUEST;
		$this->assertFalse($signedRequestLoader->auth());
    }
}  