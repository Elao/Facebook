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

use Facebook\Loader\ApplicationLoader;
use Facebook\Subscriptions\Event;
use Facebook\Subscriptions\Subscription;
/**
 * Subscription Manager allows to manager Subscription Objects
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
abstract class Manager {

    protected $subscriptions;
    protected $facebook;
    
    public function __construct(Facebook $facebook) {
        $this->facebook = $facebook;
    }

    /**
     * add or redefine a subscription
     * @param Facebook\Subscriptions\Subscription $subscription 
     * @return void
     */
    public function add(Subscription $subscription) {
        $this->subscriptions[strtolower($subscription->getObject())] = $subscription;
    }

    /**
     * add subscriptions from array of object => field
     * @param array $subscriptions
     */
    public function addFromArray(array $subscriptions) {
        foreach ($subscriptions as $sub) {
            $subscription = new Subscription($sub['object'], null, $sub['fields'], true);
            $this->add($subscription);
        }
    }
    
    /**
     * check if a subscription is defined for given object
     * @param string $object 
     * @return boolean
     */
    public function has($object) {
        return isset($this->subscriptions[strtolower($object)]);
    }

    /**
     * return a subscription by it target object
     * @param string $object
     * @return type Facebook\Subscriptions\Subscription;
     */
    public function get($object) {
        return $this->has($object) ? $this->subscriptions[strtolower($object)] : null;
    }
    
    /**
     * Retrieve subscriptions
     * @return type array
     */
    public function all() {
        return $this->subscriptions;
    }

    /**
     *  Register defined subscriptions on facebook
     *  throw \Exception
     */
    public function registerSubscriptions() {
        $applicationLoader = new ApplicationLoader($this->facebook);
        $this->facebook->getSession($application, null, true);
        foreach ($this->getSubscriptions() as $subscription){
            $params = array (
                'object'        => $subscription->getObject(),
                'fields'        => $subscription->getFields(),
                'callback_url'  => $this->facebook->getConfiguration()->getSubscriptionsCallback(),
                'verify_token'  => $this->facebook->getConfiguration()->getSubscriptionsVerifyToken()
            );
            $errors = array();
            try{
                $this->facebook->api('/%app_id%/subscriptions', 'POST', $params);
            }catch(Exception $e){ $errors[] = $e->getMessage(); }
        }
        
        if (count($errors)){
            throw new Exception(implode(',', $errors));
        }
    }

    public function retrieveSubscriptions() {
        $applicationLoader = new ApplicationLoader($this->facebook);
        $this->facebook->getSession($application, null, true);
        
        $subscriptions = $facebook->api('/%app_id%/subscriptions');
        
        foreach ($subscriptions as $_subscription) {
            $subscription = new Subscription($_subscription['object'], $_subscription['callback_url'], implode(',', $_subscription['fields']), $_subscription['active']);
            $this->add($subscription);
        }      
        return $this->all();
    }
    
    abstract public function handleEvent(Event $event);
}
