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
abstract class Event {
    private $object;
    
    public function setObject($object) {
        $this->object = $object;
    }
    
    public function getObject() {
        return $this->object;
    }


}
