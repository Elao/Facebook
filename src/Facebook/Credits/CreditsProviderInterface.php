<?php

namespace Facebook\Credits;

use Facebook\Credits\OrderInterface;
use Facebook\Credits\ItemInterface;

Interface CreditsProviderInterface {

    /**
     * Given an item code (from order_info), must return a credit item Interface
     * @return ItemInterface
     */
    public function getItemFromCode($code);
    
    /**
     * Handle and order
     */
    public function processOrder(OrderInterface $order);

    /**
     * Create and return an OrderInterface object
     * @return OrderInterface
     */
    public function createOrder();
    
    /**
     * Muse return an OrderInterface object
     * @return OrderInterface
     */
    public function getOrder($orderId);
    
}