<?php 
namespace Facebook\Test;	

class TestLogger
{
	public function debug($msg) {
		echo sprintf("[DEBUG] %s \n", $msg);
	}
}