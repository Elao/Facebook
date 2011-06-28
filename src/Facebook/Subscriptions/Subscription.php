<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Subscriptions;

/**
 * Subscription represents a Subscription object
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class Subscription {

    private $object;
    private $callback;
    private $fields;
    private $active;

    public function __construct($object = null, $callback = null, $fields = null, $active = true) {
        
        $this->object   = $object;
        $this->callback = $callback;
        $this->fields   = $fields;
        $this->active   = $active;
    }

    /**
     * @return the $object
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * @return the $callback
     */
    public function getCallback() {
        return $this->callback;
    }

    /**
     * @return the $fields
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return the $active
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param field_type $object
     */
    public function setObject($object) {
        $this->object = $object;
    }

    /**
     * @param field_type $callback
     */
    public function setCallback($callback) {
        $this->callback = $callback;
    }

    /**
     * @param field_type $fields
     */
    public function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * @param field_type $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

}
