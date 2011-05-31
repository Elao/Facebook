<?php
namespace Facebook\Test\Loader;

use Facebook\Loader\CookieLoader;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;




class CookieLoaderTest extends BaseTest
{
	protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testLoader()
    {
    	$cookieLoader = new CookieLoader($this->facebook);
    	$cookieName   = 'fbs_'.$this->configuration->getAppId();
    	
    	// Test support
    	$this->assertFalse($cookieLoader->support(), "Cookie Loader shouldn't be supported");
    	$_COOKIE[$cookieName] = self::$VALID_COOKIE;
    	$this->assertTrue($cookieLoader->support(), "Cookie Loader mode should be supported");
    	
    	// Test auth
		$this->assertInstanceOf('\Facebook\Session', $cookieLoader->auth());
    }
}  