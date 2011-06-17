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
class FSDK_Autoloader
{
    /**
     * Registers FSDK_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param  string  $class  A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        if (0 !== strpos($class, 'Facebook')) {
            return;
        }

        if (file_exists($file = dirname(__FILE__).'/../'.str_replace('\\', '/', $class).'.php')) {
            require $file;
        }
    }
}
