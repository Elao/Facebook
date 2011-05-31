<?php
namespace Facebook\Test;

use Facebook\Dumper\SessionDumper;

use Facebook\Loader\SessionLoader;

use Facebook\Session;

use Facebook\Loader\CookieLoader;

use Facebook\Loader\SignedRequestLoader;
use Facebook\Dumper\CookieDumper;

use Facebook\Test\BaseTest;
use Facebook\Test\TestLogger;

use Facebook\Facebook;
use Facebook\Configuration;
use Facebook\Requester\CurlRequester;


use Facebook\Exception\AuthException;


class FacebookTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$_SERVER['HTTP_HOST']   = 'tmp';
    	$_SERVER['REQUEST_URI'] = '/index.php';
    	
		$this->configuration = new Configuration(array(
        	'appId' 		=> $this->appId,
        	'appSecret' 	=> $this->appSecret,
     		'debug'			=> true
		));
    	
		$requester 	  	= new CurlRequester();
        $logger	   	  	= new TestLogger();
        $this->facebook = new Facebook($this->configuration, $requester, $logger);
    }
    
    public function testConstructor()
    {
        // Check request
		$this->assertInstanceOf('\Facebook\Requester\CurlRequester', $this->facebook->getRequester());
        
		// Check logger
		$this->assertInstanceOf('\Facebook\Test\TestLogger', $this->facebook->getLogger());
		$this->assertEquals($this->appId, $this->facebook->getAppId());
		$this->assertEquals($this->appSecret, $this->facebook->getAppSecret());
		
    }
    
    public function testException()
    {
		// As no loader are set, should raise a configuration exception
		$this->setExpectedException('\Facebook\Exception\AuthException');
		$this->facebook->getSession();		
    }
    
    public function testLogger()
    {
    	$this->facebook->setLogger(null);
    	$this->assertNull($this->facebook->getLogger());
    	$this->facebook->setLogger(new TestLogger());
    	$this->assertInstanceOf('Facebook\Test\TestLogger',$this->facebook->getLogger());
    }
    
    public function testRequester()
    {
    	$this->facebook->setRequester(new CurlRequester());
    	$this->assertInstanceOf('Facebook\Requester\CurlRequester',$this->facebook->getRequester());
    }
    
    public function testLoaders()
    {
    	$this->facebook->addLoader(new SignedRequestLoader($this->facebook));
    	$this->assertInternalType('array', $this->facebook->getLoaders());
    	$this->assertEquals(1, count($this->facebook->getLoaders()));
    }
    
    public function testDumpers()
    {
    	$this->facebook->addDumper(new CookieDumper($this->facebook));
    	$this->assertInternalType('array', $this->facebook->getDumpers());
    	$this->assertEquals(1, count($this->facebook->getDumpers()));
    }
    
    public function testLoginUrl()
    {
    	$expected = "https://www.facebook.com/login.php?api_key=1&cancel_url=2&display=page&fbconnect=3&next=4&return_session=5&session_version=6&v=7&canvas=8&req_perms=p1%2Cp2";
    	$params   = array(
    		'api_key'         => 1,
	        'cancel_url'      => 2,
	        'fbconnect'       => 3,
	        'next'            => 4,
	        'return_session'  => 5,
	        'session_version' => 6,
	        'v'               => 7,
	  		'canvas'		  => 8,
	  		'req_perms'		  => implode(',', array('p1', 'p2'))
    	);
    	// Display from default
    	$this->assertEquals($expected, $this->facebook->getLoginUrl($params));
    }
    
    public function testLogoutUrl()
    {
    	// We don't need to create a real session object
    	$stub = $this->getMock('Facebook\Facebook', array('getAccessToken'), array($this->configuration, new CurlRequester()));
        $stub->expects($this->any())->method('getAccessToken')->will($this->returnValue('foobar'));
    	
    	$expected = "https://www.facebook.com/logout.php?next=1&access_token=foobar";
    	$params = array(
    	    'next'         => 1                                                                                      
    	);
    	
    	$this->assertEquals($expected, $stub->getLogoutUrl($params));
    }
    
    public function testLoginStatusUrl()
    {
    	$expected = "https://www.facebook.com/extern/login_status.php?api_key=1&no_session=2&no_user=3&ok_session=4&session_version=5";
    	$params = array(
	    	'api_key'         => 1,                                                                                          
	     	'no_session'      => 2,                                                                                     
	     	'no_user'         => 3,                                                                                     
	     	'ok_session'      => 4,                                                                                     
	     	'session_version' => 5,  
    	);	
    	
    	$this->assertEquals($expected, $this->facebook->getLoginStatusUrl($params));
    }
    
    
    public function testSession()
    {
    	parent::loadDefaultConfiguration();
    	
    	// We dump a session so, the getSession() will work
    	$session = new Session($this->facebook, array('access_token' => 'mytoken', 'uid' => '123'));
    	$dumper  = new SessionDumper($this->facebook);
    	$dumper->dump($session);
    	$loader  = new SessionLoader($this->facebook);
    	
    	$this->facebook->addLoader(new SessionLoader($this->facebook));
    	$this->facebook->addDumper(new SessionDumper($this->facebook));
    	
    	$this->assertInstanceOf('Facebook\\Session', $this->facebook->getSession());
    	
    	$this->assertInstanceOf('Facebook\\Session', $this->facebook->getSession(array($loader), array($dumper), false));

    	$this->assertInstanceOf('Facebook\\Session', $this->facebook->getSession($loader, $dumper, true));
    	
    	$this->assertEquals('mytoken', $this->facebook->getAccessToken());
    	
    	$this->setExpectedException('\Facebook\Exception\AuthException');
    	$this->facebook->getSession(array(), array(), true);
    	$this->assertFalse($this->facebook->getAccessToken());
    	
    }
    
    
}