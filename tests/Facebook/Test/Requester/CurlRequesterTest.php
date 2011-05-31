<?php
use Facebook\Test\BaseTest;

use Facebook\Requester\CurlRequester;

class CurlRequesterTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->requester = new CurlRequester();
    }
    
    public function testLoader()
    {
		
    	$url 	   = 'https://graph.facebook.com/btaylor';
		$params    = array('method' => 'GET');
		
		$result  = $this->requester->request($url, $params);
    	
		$this->assertInternalType('array', $result, "Result should be an array");
		$this->assertEquals('220439', $result['id']);
		
		
		
		$this->assertFalse($this->requester->request('badurlspecified', array()));
    }
}  