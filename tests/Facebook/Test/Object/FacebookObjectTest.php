<?php
namespace Facebook\Test\Object;

use Facebook\Object\FacebookCollection;

use Facebook\Object\FacebookObject;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;

use Facebook\Requester\CurlRequester;
use \Exception;

use Facebook\Loader\ApplicationLoader;

class FacebookObjectTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testObject()
    {
		$object = new FacebookObject($this->facebook, array('id' => 'testid', 'property1' => 'p1'));
    	$this->assertTrue($object->hasId());
		$this->assertEquals($object->getId(), 'testid');
		$this->assertEquals($object->get('property1'), 'p1');
		$this->assertNull($object->get('unknowproperty'));
		
		$this->assertEquals($object->getProperty1(), 'p1');
		$this->assertNull($object->getUnknowproperty());
		
		// Fetch without id
		$object = new FacebookObject($this->facebook, array('p1' => 'p1'));
		$this->assertFalse($object->fetchFriends());
		$this->assertFalse($object->hasId());

		$apiReturn = array(
			array('name' => 'foo', 'id' => '1'),
			array('name' => 'bar', 'id' => '2'),
			array('name' => 'foobar', 'id' => '3')
		);
		$collection = new FacebookCollection($this->facebook, $apiReturn);
		
		// Simulate api call
    	$stub = $this->getMock('Facebook\Facebook', array('api'), array($this->configuration, new CurlRequester()));
        $stub->expects($this->any())->method('api')->will($this->returnValue($collection));
    	
    	$object = new FacebookObject($stub, array('id' => '123'));
		
    	$this->assertSame($collection, $object->fetchFriends());
    	
    	$this->setExpectedException('\Exception');
    	$object->unknowMethodToCall();
    }
    
    public function testArrayAccess()
    {
    	$data = array(
			"id" => "id1",
    		"p1" => "v1",
    		"p2" => "v2",
    		"p3" => "v3"    		
    	);
    	
    	$object = new FacebookObject($this->facebook, $data);
    	
    	// Testing offset get
    	$this->assertEquals("id1", $object["id"]);
    	$this->assertEquals("v1", $object["p1"]);
    	
    	// Testing offset set
    	$object["p4"] = "v4";
    	$this->assertEquals("v4", $object["p4"]);
    	
    	// Testing isset
    	$this->assertFalse(isset($object["unknow"]));
    	
    	// Testing unset
    	unset($object["p2"]);
    	$this->assertFalse(isset($object["p2"]));
    }
    
    public function testCount()
    {
    	$data = array(
			"id" => "id1",
    		"p1" => "v1",
    		"p2" => "v2",
    		"p3" => "v3"    		
    	);
    	
    	$object = new FacebookObject($this->facebook, $data);    	
    	$this->assertEquals(4, count($object));
    }
    
    public function testIterator()
    {
    	$data = array(
			"id" => "id1",
    		"p1" => "v1",
    		"p2" => "v2",
    		"p3" => "v3"    		
    	);
    	
    	$object = new FacebookObject($this->facebook, $data);
    	$object[] 	  = "glut";
    	$object["hi"] = "blaat";
    	
    	$i=1;
    	foreach ($object as $key => $value){
    		switch($i)
    		{
    			case 1: $this->assertEquals($key, "id");$this->assertEquals($value, "id1");   break;
    			case 2: $this->assertEquals($key, "p1");$this->assertEquals($value, "v1"); 	  break;
    			case 3: $this->assertEquals($key, "p2");$this->assertEquals($value, "v2"); 	  break;
    			case 4: $this->assertEquals($key, "p3");$this->assertEquals($value, "v3"); 	  break;
    			case 5: $this->assertEquals($key, 0);$this->assertEquals($value, "glut"); 	  break;
    			case 6: $this->assertEquals($key, "hi");$this->assertEquals($value, "blaat"); break;
    		}	
			$i++;			    		
    	}
    	unset($object["p2"]);
    	$i=1;
		foreach ($object as $key => $value){
    		switch($i)
    		{
    			case 1: $this->assertEquals($key, "id");$this->assertEquals($value, "id1"); 	break;
    			case 2: $this->assertEquals($key, "p1");$this->assertEquals($value, "v1"); 		break;
    			case 3: $this->assertEquals($key, "p3");$this->assertEquals($value, "v3"); 		break;
    			case 4: $this->assertEquals($key, 0);$this->assertEquals($value, "glut"); 		break;
    			case 5: $this->assertEquals($key, "hi");$this->assertEquals($value, "blaat"); 	break;
    		}	
			$i++;			    		
    	}
    }
    
}  