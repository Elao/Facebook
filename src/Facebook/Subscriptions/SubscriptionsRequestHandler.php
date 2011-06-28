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
use Facebook\Subscriptions\UserEvent;

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
        foreach (array('hub_mode', 'hub_verify_token', 'hub_chanllenge') as $check){
            if (!isset($subscriptionsRequest[$check])){
                return false;
            }
        }
        
        if ($subscriptionsRequest['hub_mode'] == 'subscribe' 
            && $subscriptionsRequest['hub_verify_token'] == $this->facebook->getConfiguration()->getSubscriptionsVerifyToken()) {
            return $subscriptionsRequest['hub_challenge'];
        }
    }

    public function getResponsePost(array $subscriptionsRequest) {
        try{
            $event = new UserEvent($subscriptionsRequest);
            $this->getSubscriptionsManager()->handleEvent($event);    
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}

