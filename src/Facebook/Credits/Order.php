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
class Order implements OrderInterface {
    
    protected $status;
    protected $id;
    protected $items;
    protected $buyerId;
    protected $receiverId;
    
    public function __construct($id = null, $status = null) {
        $this->setId($id);
        $this->setStatus($status);
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getId($id) {
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
    
    public function getItems() {
        return $this->items;
    }
    
    public function setItems(array $items) {
        $this->items = $items;
    }
    
    public function addItem($item) {
        $this->items[] = $item;
    }
}