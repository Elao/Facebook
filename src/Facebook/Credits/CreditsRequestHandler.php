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
namespace Facebook\Credits;

use Facebook\Facebook;
use Facebook\Credits\CreditsProviderInterface;
use Facebook\Credits\ItemInterface;

class CreditsRequestHandler implements CreditsRequestHandlerInterface {

    protected $facebook;
    protected $creditsProvider;
    protected $orderManager;
    
    public function __construct(Facebook $facebook) {
        $this->facebook     = $facebook;
    }
    
    public function getCreditsProvider() {
        return $this->creditsProvider;
    }
    
    public function setCreditsProvider(CreditsProviderInterface $creditsProvider) {
        $this->creditsProvider = $creditsProvider;
    }
    
    public function getOrderManager() {
        return $this->orderManager;
    }
    
    public function setOrderManager($orderManager) {
        $this->orderManager = $orderManager;
    }

    public function getResponse(array $creditsRequest) {

        $signedRequest = $creditsRequest['signed_request'];
        $data = array ();

        try {
            $payload = $this->facebook->parseSignedRequest($signedRequest);
        } catch (Exception $e) {
            return false;
        }

        $credits = isset($payload['credits']) ? $payload['credits'] : false;

        // retrieve all params passed in
        $method = $creditsRequest['method'];

        if ($method == 'payments_status_update') {
            $status   = $credits['status'];
            $details  = $credits['order_details'];
            $orderId  = $credits['order_id'];

            // Créer une order ou l'updater
            
            // Increment user crédits on the website
            // write your logic here, determine the state you wanna move to
            if ($status == 'placed') {
                // Create an order and process it
                $order = $this->getCreditsProvider()->createOrder();
                $order->setId($orderId);
                $order->setStatus(OrderInterface::STATUS_PLACED);
                $status = 'settled';
            } elseif ($status == 'settled') {
                $order = $this->getCreditsProvider()->getOrder($orderId);
                $order->setStatus(OrderInterface::STATUS_SETTLED);
            }
            
            $order->setDetails($details);
            $this->getCreditsProvider()->processOrder($order);
            
            $data['content']['status'] = $status;

            // compose returning data array_change_key_case
            $data['content']['order_id'] = $orderId;
        } else if ($method == 'payments_get_items') {

            // remove escape characters
            $orderInfo = $credits ? stripcslashes($credits['order_info']) : '';
            if (is_string($orderInfo)) {
                $item = $this->getCreditsProvider()->getItemFromCode($orderInfo);
                if (!$item) {
                    // Throw Exception
                }
                
                if (!$item instanceof ItemInterface) {
                    // Throw Exception
                }
                
                $data['content'] = array ($item->toArray());
            }
        }

        // required by api_fetch_response()
        $data['method'] = $method;

        // send data back
        return $data;
    }

}

