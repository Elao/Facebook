<?php

/*
 * This file is part of the Facebook package.
 *
 * (c) 2010-2011 Facebook Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Facebook\\Test\\')) {
        $file = __DIR__ . '/../tests/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    } elseif (0 === strpos($class, 'Facebook\\')) {
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
});