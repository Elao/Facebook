<?php
namespace Facebook\Test;

use Facebook\Test\BaseTest;
use Facebook\Configuration;


class ConfigurationTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    }
    
    public function testConstructor()
    {
        $this->configuration = new Configuration(array(
        	'appId' 		 => $this->appId,
        	'appSecret' 	 => $this->appSecret,
        	'anything'		 => 'anywhere',
        	'appFacebookUrl' => 'myapp',
        	'permissions'	 => array('perm1', 'perm2') 
        ));
    	
        $this->assertEquals($this->appId, 	  	  	$this->configuration->getAppId(), 	 "Retrieve App Id");
        $this->assertEquals($this->appSecret, 	  	$this->configuration->getAppSecret(),  "Retrieve App Secret");
        $this->assertEquals('anywhere', 		  	$this->configuration->get('anything'), "Retrieve Random param");
        $this->assertEquals('myapp',			  	$this->configuration->getAppFacebookUrl(), "Retrieve App Facebook Url");
        $this->assertEquals(array('perm1', 'perm2'),$this->configuration->getAppPermissions(), "Retrieve Permissions");
        $this->assertNull($this->configuration->get('nonexistingparam'), "Retrieve unset params");
        
        
    }
    
    public function testException()
    {
		// As no loader are set, should raise a configuration exception
		$this->setExpectedException('\Facebook\Exception\ConfigurationException');
		new Configuration(array());
    }
    
    public function testException2()
    {
    	// As no loader are set, should raise a configuration exception
		$this->setExpectedException('\Facebook\Exception\ConfigurationException');
		new Configuration(array('appId' => '123'));
    }
    
    

}