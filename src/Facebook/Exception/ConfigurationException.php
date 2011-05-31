<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Exception;

class ConfigurationException extends \Exception
{

  public function __construct($message) {
		parent::__construct($message, null, null);
  }

  public function __toString() {
    return $this->message;
  }
}
