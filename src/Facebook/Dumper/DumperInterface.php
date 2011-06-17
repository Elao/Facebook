<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Dumper;

use Facebook\Session;

/**
 * Cookie Dumper dump a Session Object into a cookie
 *
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
Interface DumperInterface {

    // Dump the session... somewhere. Return true if success, false otherwise
    public function dump(Session $session);

    // Return true, if no more dumper should be call after this one
    public function breakOnSucess();
}