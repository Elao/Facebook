<?php
namespace Facebook\Test\Dumper;

use Facebook\Dumper\SessionDumper;

use Facebook\Session;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;

use Facebook\Dumper\CookieDumper;


class SessionDumperTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testDumper()
    {
		$dumper  = new SessionDumper($this->facebook);
    	$session = new Session($this->facebook, array('access_token' => 'mytoken', 'uid' => 'myuid'));
		
		$this->assertTrue($dumper->dump($session));
		
		$this->assertNotNull($_SESSION[$dumper->getSessionKey()]); 
    	$this->assertInternalType('array', unserialize($_SESSION[$dumper->getSessionKey()]));
		
    }
}  