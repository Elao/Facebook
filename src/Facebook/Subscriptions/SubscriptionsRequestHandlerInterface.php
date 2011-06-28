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
 * Credits request handler interface
 * Handle a credits Request
 * @package    Facebook SDK
 * @author     Vincent BOUZERAN <vincent.bouzeran@elao.com>
 */
namespace Facebook\Subscriptions;

Interface SubscriptionsRequestHandlerInterface {

    public function getResponseGet(array $subscriptionsRequest);
    
    public function getResponsePost(array $subscriptionsRequest);

}

