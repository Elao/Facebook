<?php

namespace Facebook\Credits;

Interface OrderInterface {
    const STATUS_PLACED     = 1;
    const STATUS_RESERVED   = 2;
    const STATUS_SETTLED    = 3;
    const STATUS_CANCELED   = 4;
    const STATUS_REFUNDED   = 5;
    
    /* Return order status */
    public function getStatus();
    
    /* Return order id */
    public function getId();
    
    /* Return items of this order */
    public function getItems();
    
    /* Return the facebook id of the buyer */
    public function getBuyerId();
    
    /* Return the facebook id of the receiver */
    public function getReceiverId();
    
    public function setStatus($status);
    
    public function setId($id);
    
    public function setDetails($details);
    
}