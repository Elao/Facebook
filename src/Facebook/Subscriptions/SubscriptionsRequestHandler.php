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
 * Subscription request handler
 * Given a subscriptions request (GET or POST)
 * Return the challenge, or call the subscription manager
 * @package    Facebook SDK
 * @author     Vincent BOUZERAN <vincent.bouzeran@elao.com>
 */
namespace Facebook\Subscriptions;

use Facebook\Facebook;
use Facebook\Subscriptions\SubscriptionsProviderInterface;
use Facebook\Subscriptions\Manager;

class SubscriptionsRequestHandler implements SubscriptionsRequestHandlerInterface {

    protected $facebook;
    protected $subscriptionsManager;
    
    public function __construct(Facebook $facebook) {
        $this->facebook     = $facebook;
    }
    
    public function setSubscriptionsManager($subscriptionsManager) {
        if (!$subscriptionsManager instanceof Manager){
            throw new Exception("The subscription manager must extends Manager");
        }
        $this->subscriptionsManager = $subscriptionsManager;
    }
    
    public function getSubscriptionsManager() {
        return $this->subscriptionsManager;
    }
    
    public function getResponseGet(array $subscriptionsRequest) {
        
        if ($subscriptionsRequest['hub_mode'] == 'subscribe' 
            && $subscriptionsRequest['hub_verify_token'] == $this->facebook->getConfiguration()->getSubscriptionsVerifyToken()) {
            return $subscriptionsRequest['hub_challenge'];
        }
    }

    public function getResponsePost(array $subscriptionsRequest) {
        throw new \Exception("Got a subscription post request :D");
        
        // Build the event
        
        // Pass it to manager
        
        
        $this->getSubscriptionsManager()->handleEvent($event);
    }
}

