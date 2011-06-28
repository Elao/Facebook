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

use Facebook\Object\Collection;
use \Exception;

/**
 * FacebookObject represents a Facebook Object
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class FacebookObject implements \ArrayAccess, \Iterator, \Countable {

    protected $facebook;
    protected $id;
    protected $dataNames = array();
    protected $count = 0;
    protected $data = array();

    public function __construct($facebook, array $data) {
        $this->facebook = $facebook;
        $this->data = $data;
        $this->id = isset($data['id']) ? $data['id'] : null;
    }

    public function __toString() {
        return $this->id;
    }

    public function getId() {
        return $this->id;
    }

    public function get($property) {
        if (isset($this->data[$property])) {
            return $this->data[$property];
        }
        return null;
    }

    public function hasId() {
        return !is_null($this->id);
    }

    public function fetch($connexion, $options = array()) {
        if (!$this->hasId()) {
            return false;
        }
        $method = 'GET';
        $params = array();

        $call   = '/' . $this->getId() . '/' . $connexion;
        $result = $this->facebook->api($call, $method, $params);

        if (!$result instanceof FacebookCollection) {
            return array();
        }
        return $result;
    }

    public function __call($method, $arguments) {

        if (substr($method, 0, 3) == 'get') {
            // Want a property
            // getFirstName => first_name
            $property = $this->methodToProperty(substr($method, 3));
            return call_user_func(array($this, 'get'), $property);
        }

        if (substr($method, 0, 5) == 'fetch') {
            // Want a connexion
            $connexion = $this->methodToProperty(substr($method, 5));
            return call_user_func(array($this, 'fetch'), $connexion);
        }
        throw new Exception(sprintf('Unknown method %s::%s', get_class($this), $method));
    }

    public function methodToProperty($method) {
        $property = $method;
        $property = str_replace('::', '/', $property);
        $replace = array(
            '/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
            '/([a-z\d])([A-Z])/'     => '\\1_\\2'
        );
        $property = preg_replace(array_keys($replace), array_values($replace), $property);

        return strtolower($property);
    }

    /** ArrayAccess * */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        } else {
            return null;
        }
    }

    public function offsetSet($offset, $value) {
        if ($offset) {
            $this->data[$offset] = $value;
        } else {
            $this->data[] = $value;
        }
    }

    public function offsetUnset($offset) {
        if (isset($this->data[$offset])) {
            unset($this->data[$offset]);
        }
    }

    /** Iterator * */
    public function current() {
        return $this[current($this->dataNames)];
    }

    public function key() {
        return current($this->dataNames);
    }

    public function next() {
        next($this->dataNames);
        --$this->count;
    }

    public function rewind() {
        $this->dataNames = array_keys($this->data);
        reset($this->dataNames);
        $this->count = count($this->dataNames);
    }

    public function valid() {
        return $this->count > 0;
    }

    /** Countable * */
    public function count() {
        return count($this->data);
    }

}