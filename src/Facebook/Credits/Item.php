<?php

namespace Facebook\Credits;

use Facebook\Credits\ItemInterface;

class Item implements ItemInterface {
    
    protected $itemId;
    protected $title;
    protected $price;
    protected $description;
    protected $imageUrl;
    protected $productUrl;

    public function getItemId() {
        return $this->itemId;
    }
    
    public function setItemId($itemId) {
        $this->itemId = $itemId;
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getPrice() {
        return $this->price; 
    }

    public function setPrice($price) {
        $this->price = $price;
    }
    
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function getProductUrl() {
        return $this->productUrl;
    }

    public function setProductUrl($productUrl) {
        $this->productUrl = $productUrl;
    }

    public function toArray() {
        return array(
            'item_id'       => $this->getItemId(),
            'title'         => $this->getTitle(),
            'price'         => $this->getPrice(),
            'description'   => $this->getDescription(),
            'image_url'     => $this->getImageUrl(),
            'product_url'   => $this->getProductUrl()   
        );
    }
}