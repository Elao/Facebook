<?php
/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Credits controller classe
 * Given a credits request (with signed request containing information)
 * Found the requested item or update order associated
 * @package    Facebook SDK
 * @author     Vincent BOUZERAN <vincent.bouzeran@elao.com>
 */
namespace Facebook\Subscriptions;

use Facebook\Facebook;
use Facebook\Subscriptions\SubscriptionsProviderInterface;

class SubscriptionsRequestHandler implements SubscriptionsRequestHandlerInterface {

    protected $facebook;
    
    public function __construct(Facebook $facebook) {
        $this->facebook     = $facebook;
    }
    
    public function getResponseGet(array $subscriptionsRequest) {
        
        if ($subscriptionsRequest['hub_mode'] == 'subscribe' 
         && $subscriptionsRequest['hub_verify_token'] == $this->facebook->getConfiguration()->getSubscriptionsVerifyToken()) {
            return $subscriptionsRequest['hub_challenge'];
        }
    }

    public function getResponsePost() {
        
    }
}

