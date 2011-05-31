<?php
namespace Facebook\Test\Loader;

use Facebook\Loader\ApplicationLoader;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;


class ApplicationTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
	public function testLoader()
    {
		$applicationLoader = new ApplicationLoader($this->facebook);
    	
    	// Test support()
		$this->assertTrue($applicationLoader->support());
    	
    	// Test auth
		$this->assertInstanceOf('\Facebook\Session', $applicationLoader->auth());
    }
    
	public function testLoaderWithCode()
    {
		$applicationLoader = new ApplicationLoader($this->facebook, 'arbitrarycode');
    	
		$this->assertEquals('arbitrarycode', $applicationLoader->getCode());
		
    	// Test support()
		$this->assertTrue($applicationLoader->support());
    	
    	// Test auth
		$this->assertInstanceOf('\Facebook\Session', $applicationLoader->auth());
		
		
		// With a bad app Id
		$this->appId = "bkdlez";
		$this->loadDefaultConfiguration();
		$applicationLoader = new ApplicationLoader($this->facebook);
		$this->assertFalse($applicationLoader->auth());
    }
}  