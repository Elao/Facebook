<?php
namespace Facebook\Test\Object;

use Facebook\Object\FacebookCollection;
use Facebook\Object\FacebookObject;

use Facebook\Test\BaseTest;
use Facebook\Facebook;
use Facebook\Configuration;

use Facebook\Loader\ApplicationLoader;

class FacebookCollectionTest extends BaseTest
{
    protected function setUp()
    {
    	parent::setUp();
    	$this->loadDefaultConfiguration();
    }
    
    public function testCollection()
    {
    	$objects = array(
    		array('id' => 'ob1', 'data1' => 'd1'),
    		array('id' => 'ob2', 'data1' => 'd2')
    	);
    	
		$collection = new FacebookCollection($this->facebook, $objects);
    	
    	$this->assertSame(array('d1', 'd2'), $collection->getData1());
		
		$object  = new FacebookObject($this->facebook, array('p1' => 'p1'));
		$object2 = new FacebookObject($this->facebook, array('p1' => 'p2'));
		$object3 = new FacebookObject($this->facebook, array('p1' => 'p3'));
		$object4 = new FacebookObject($this->facebook, array('p1' => 'p4'));
		
		$objects = array($object, $object2, $object3, $object4);
		
		$collection = new FacebookCollection($this->facebook, $objects);
		
		$this->assertSame(array('p1', 'p2', 'p3', 'p4'), $collection->getP1());
		
		// Test fetching
		// Simulate api call
		$collectionObj1 = new FacebookCollection($this->facebook, array(array('id' => 'id11'), array('id' => 'id12')));
		$collectionObj2 = new FacebookCollection($this->facebook, array(array('id' => 'id21'), array('id' => 'id22')));
		
    	$stubObj1 = $this->getMock('Facebook\Object\FacebookObject', array('fetch'), array($this->facebook, array('id' => 'id1', 'p1' => '1')));
        $stubObj1->expects($this->any())->method('fetch')->will($this->returnValue($collectionObj1));
    	
        $stubObj2 = $this->getMock('Facebook\Object\FacebookObject', array('fetch'), array($this->facebook, array('id' => 'id2', 'p1' => '1')));
        $stubObj2->expects($this->any())->method('fetch')->will($this->returnValue($collectionObj2));
        
    	$collection = new FacebookCollection($this->facebook, array($stubObj1, $stubObj2));
    	
    	$this->assertSame(array('id1', 'id2'), $collection->getId());
    	
    	$this->assertSame(array('id11', 'id12', 'id21', 'id22'), $collection->fetchFriends()->getId());

		$this->setExpectedException('\Exception');
    	$collection->unknowMethodToCall();
    }
    
    public function testBadConstructor()
    {
    	$this->setExpectedException('\Exception');
    	new FacebookCollection($this->facebook, array('glop'));
    }
    
    public function testArrayAccess()
    {
     	$objects = array(
    		array('id' => 'ob1', 'data1' => 'd1'),
    		array('id' => 'ob2', 'data1' => 'd2')
    	);
    	
    	$collection = new FacebookCollection($this->facebook, $objects);
    	
    	// Testing offset get
    	$this->assertEquals("ob1",  $collection[0]["id"]);
    	$this->assertEquals("ob2",  $collection[1]["id"]);
    	
    	// Testing offset set
    	$collection[] = new FacebookObject($this->facebook, array("id" => "ob3"));
    	
    	$this->assertEquals("ob3",  $collection[2]["id"]);
    	
    	// Testing isset
    	$this->assertFalse(isset($collection[9]));
    	
    	// Testing unset
    	unset($collection[1]);
    	$this->assertFalse(isset($collection[1]));
    }
    
    public function testCount()
    {
     	$objects = array(
    		array('id' => 'ob1', 'data1' => 'd1'),
    		array('id' => 'ob2', 'data1' => 'd2')
    	);
    	
    	$collection = new FacebookCollection($this->facebook, $objects);
    	$this->assertEquals(2, count($collection));
    	$collection[] = new FacebookObject($this->facebook, array('id' => "test"));
    	$this->assertEquals(3, count($collection));
    }
    
	public function testIterator()
    {
		$objects = array(
    		array('id' => 'ob1', 'data1' => 'd1'),
    		array('id' => 'ob2', 'data1' => 'd2')
    	);
    	
    	$collection = new FacebookCollection($this->facebook, $objects);
    	$this->assertEquals(2, count($collection));
    	$collection[] = new FacebookObject($this->facebook, array('id' => "test"));
    	$this->assertEquals(3, count($collection));
    	
    	$i=1;
    	foreach ($collection as $key => $object){
    		switch($i)
    		{
    			case 1: $this->assertEquals($key, 0);$this->assertEquals($object->getId(), "ob1");    break;
    			case 2: $this->assertEquals($key, 1);$this->assertEquals($object->getId(), "ob2"); 	  break;
    			case 3: $this->assertEquals($key, 2);$this->assertEquals($object->getId(), "test");	  break;
    		}	
			$i++;			    		
    	}
    	unset($collection[1]);
    	$i=1;
		foreach ($collection as $key => $object){
    		switch($i)
    		{
    			case 1: $this->assertEquals($key, 0);$this->assertEquals($object->getId(), "ob1");    break;
    			case 2: $this->assertEquals($key, 2);$this->assertEquals($object->getId(), "test");	  break;
    		}	
			$i++;			    		
    	}
    }
    
    public function testMethodToProperty()
    {
		$objects = array(
    		array('id' => 'ob1', 'first_name' => 'd1'),
    		array('id' => 'ob2', 'first_name' => 'd2')
    	);
    	
    	$collection = new FacebookCollection($this->facebook, $objects);

    	$this->assertSame(array('d1', 'd2'), $collection->getFirstName());
    }
    
}  