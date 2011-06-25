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
 * Credit Order class implementing OrderInterface
 * Represents an facebook credits order
 *
 * @package    Facebook SDK
 * @author     Vincent BOUZERAN <vincent.bouzeran@elao.com>
 */

namespace Facebook\Credits;

use Facebook\Credits\OrderInterface;

class Order implements OrderInterface {
    
    protected $status;
    protected $id;
    protected $buyerId;
    protected $receiverId;
    protected $appId;
    protected $amount;
    protected $timePlaced;

    protected $itemIds;
    
    public function __construct($id = null, $status = null) {
        $this->setId($id);
        $this->setStatus($status);
    }
    
    public function setDetails(array $details) {
        $orderId    = $details["order_id"];
        $buyerId    = $details["buyer"];
        $appId      = $details["app"];
        $receiverId = $details["receiver"];
        $amount     = $details["amount"];
        $timePlaced = $details["time_placed"];
        $items      = $details["items"];
        
        $this->id           = $orderId;
        $this->buyerId      = $buyerId;
        $this->appId        = $appId;
        $this->receiverId   = $receiverId;
        $this->amount       = $amount;
        $this->timePlaced   = $timePlaced;
        
        foreach ($items as $item) {
            $this->addItemId($item['item_id']);
        }
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getReceiverId() {
        return $this->receiverId;
    }
    
    public function setReceiverId($receiverId) {
        $this->receiverId = $receiverId;
    }
    
    public function getBuyerId() {
        return $this->buyerId;
    }
    
    public function setBuyerId($buyerId) {
        $this->buyerId = $buyerId;
    }
    
    public function getAppId() {
        return $this->appId;
    }
    
    public function setAppId($appId) {
        $this->appId = $appId;
    }
    
    public function getAmount() {
        return $this->amount;
    }
   
    public function setAmount($amount) {
        $this->amount = $amount;
    }
    
    public function getTimePlaced() {
        return $this->timePlaced;
    }
    
    public function setTimePlaced($timePlaced) {
        $this->timePlaced = $timePlaced;
    }
    
    public function getItemIds() {
        return $this->itemIds;
    }
    
    public function setItemIds($itemIds) {
        $this->itemIds = $itemIds;
    }
    
    public function addItemId($itemId) {
        $this->itemIds[] = $itemId;
    }
}