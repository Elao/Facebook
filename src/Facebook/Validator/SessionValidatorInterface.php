<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Validator;

Interface SessionValidatorInterface {

    // The method should return true, if the session object is valid
    public function isValidSession(array $sessionArray);
}