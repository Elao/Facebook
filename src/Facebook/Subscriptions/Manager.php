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

use \Facebook\Loader\ApplicationLoader;

/**
 * Subscription Manager allows to manager Subscription Objects
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class Manager {

    protected $subscriptions;
    protected $facebook;
    
    public function __construct(Facebook $facebook) {
        $this->facebook = $facebook;
    }

    // Renvoie vrai si une subscription existe pour cet objet
    public function add(Subscription $subscription) {
        $this->subscriptions[strtolower($subscription->getObject())] = $subscription;
    }

    // Renvoie vrai si une subscription existe pour cet objet
    public function has($object) {
        isset($this->subscriptions[strtolower($object)]);
    }

    // Renvoie la subscription pour un object donné
    public function get($object) {
        return $this->has($object) ? $this->subscriptions[strtolower($object)] : null;
    }
    
    // Renvoie la liste des subscriptions pour l'application courante
    public function all() {
        return $this->subscriptions;
    }

    // Effectue l'appel pour enregistrer les subscriptions définies au niveau du manager
    public function registerSubscriptions() {
        $applicationLoader = new ApplicationLoader($this->facebook);
        $this->facebook->getSession($application, null, true);
        foreach ($this->getSubscriptions() as $subscription){
            $params = array (
                'object'        => $subscription->getObject(),
                'fields'        => $subscription->getFields(),
                'callback_url'  => $subscription->getCallback(),
                'verify_token'  => $this->facebook->getConfigurtion()->getSubscriptionsVerifyToken()
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
}
