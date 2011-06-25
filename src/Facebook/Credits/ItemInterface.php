<?php

namespace Facebook\Credits;

Interface ItemInterface {

    public function getItemId();
    
    public function getTitle();

    public function getPrice();

    public function getDescription();

    public function getImageUrl();

    public function getProductUrl();
    
    public function toArray();
    
}