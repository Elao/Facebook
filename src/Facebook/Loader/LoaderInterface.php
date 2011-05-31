<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook\Loader;

Interface LoaderInterface
{
	// The method should return true, if the loader process can be use
	public function support();
	
	// This method should return a session object if succeed
	public function auth();
}