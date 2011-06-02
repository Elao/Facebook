<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Object;

use Facebook\Object\FacebookObject;

use \Exception;

/**
 * Facebook Object collection represents a collection of FacebookObject
 * it abstracts the retrieve of objects connexions
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class FacebookCollection implements \ArrayAccess, \Iterator, \Countable
{
	protected $facebook;
	protected $objectNames = array();
	protected $count     = 0;
	protected $objects = array();
	
	public function __construct($facebook, array $objects) {
		$this->facebook = $facebook;
		foreach ($objects as $object) {
			if (is_object($object) && $object instanceof FacebookObject){
				$this->objects[] = $object;
			}elseif (is_object($object) && $object instanceof FacebookCollection){
				foreach ($object->getObjects() as $obj){
					$this->objects[] = $obj;					
				}
			}elseif (is_array($object)){
				$this->objects[] = new FacebookObject($this->facebook, $object);
			}else{
				throw new Exception("Object passed to Facebook Collection must be FacebookObject, FacebookCollection or an array");	
			}	
		}
	}
	
	public function getObjects(){
		return $this->objects;
	}

	public function __call($method, $arguments) {
		if (substr($method, 0, 3) == 'get') {
				$result = array();
				foreach ($this->getObjects() as $object) {
					$result[] = call_user_func(array($object, $method));
				}
				return $result;
		}	

		if (substr($method, 0, 5) == 'fetch') {
				$result    = array();
				foreach ($this->getObjects() as $object) {
					$result[] = call_user_func(array($object, $method));
				}
				$collection = new FacebookCollection($this->facebook, $result);
				return $collection;
		}
		
		throw new Exception(sprintf('Unknown method %s::%s', get_class($this), $method));
	}
	
	/** ArrayAccess **/
	public function offsetExists ( $offset ){
		return isset($this->objects[$offset]);
	}
	
	public function offsetGet ( $offset ) {
		if (isset($this->objects[$offset])) {
			return $this->objects[$offset];
		}else{
			return null;
		}
	}
	
	public function offsetSet ( $offset , $value ) {
		if ($offset){
			$this->objects[$offset] = $value;
		}else{
			$this->objects[] = $value;
		}
	}
	
	public function offsetUnset ( $offset ) {
		if (isset($this->objects[$offset])) {
			unset($this->objects[$offset]);
		}
	}
	
	
	/** Iterator **/
	public function current () {
		return $this[current($this->objectNames)];
	}
	
	public function key () {
		return current($this->objectNames);
	}
	
	public function next () {
		next($this->objectNames);
    	--$this->count;
	}
	
	public function rewind () {
		$this->objectNames = array_keys($this->objects);
	    reset($this->objectNames);
    	$this->count = count($this->objectNames);
	}
	
	public function valid () {
		return $this->count > 0;	
	}
	
	/** Countable **/
	public function count () {
		return count($this->objects);	
	}
	
	
	
}