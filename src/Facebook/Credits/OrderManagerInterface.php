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
 * Autoloads Facebook SDK classes.
 *
 * @package    Facebook SDK
 * @author     Guewen FAIVRE <guewen.faivre@elao.com>
 */
namespace Facebook\Credits;

Interface OrderManagerInterface {
    
    /* Must return an Order Interface Object */
    public function createOrder();
    
    public function retrieveOrder($orderId);
        
    public function refundOrder(OrderInterface $order);
    
    
}