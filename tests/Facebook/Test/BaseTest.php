<?php
	namespace Facebook\Test;

	use Facebook\Configuration;
	use Facebook\Facebook;
	
	use Facebook\Requester\CurlRequester; 
	
	abstract class BaseTest extends \PHPUnit_Framework_TestCase
	{
		protected static $VALID_DATA = array(
	    	'access_token' => '117743971608120|2.vdCKd4ZIEJlHwwtrkilgKQ__.86400.1281049200-1677846385|NF_2DDNxFBznj2CuwiwabHhTAHc.',
	    	'expires'      => '1281049200',
	    	'secret'       => 'u0QiRGAwaPCyQ7JE_hiz1w__',
	    	'session_key'  => '2.vdCKd4ZIEJlHwwtrkilgKQ__.86400.1281049200-1677846385',
	    	'sig'          => '7a9b063de0bef334637832166948dcad',
	    	'uid'          => '1677846385'
  		);
  	
  		protected static $INVALID_DATA = array(
	    	'access_token' => '117743971608120|2.vdCKd4ZIEJlHwwtrkilgKQ__.86400.1281049200-1677846385|NF_2DDNxFBznj2CuwiwabHhTAHc.',
	    	'expires'      => '1281049200',
	    	'secret'       => 'u0QiRGAwaPCyQ7JE_hiz1w__',
	    	'session_key'  => '2.vdCKd4ZIEJlHwwtrkilgKQ__.86400.1281049200-1677846385',
	    	'sig'          => '7a9b063de0bef33463783d2166948dcad',
	    	'uid'          => '1677846385',
  		);
  		
  		protected static $VALID_COOKIE   = "uid=100001332240827&access_token=139577522771663%7C2.AQCTrqXvZYyhNGqH.3600.1305712800.1-100001332240827%7CcDdt_xUv27i9KYDIZynX9rdF3SE&expires=1305712800&sig=44d7f5dd8bb47f3a7e2ad51e8d7379e4";
		protected static $INVALID_COOKIE = "uid=100001332240827&access_token=139577522771663%7C2.AQCTrqXvZYyhNGqH.3600.1305712800.1-100001332240827%7CcDdt_xUv27i9KYDIZynX9rdF3SE&expires=1305712800&sig=44d7f5dd8bb47f3a7e2ad51e8d737dd9e4";
	
		protected static $VALID_SIGNED_REQUEST   = 'IGYtoeWnxtYa75UmzfegYb-VMj7JeocYicSuCx39Tug.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImV4cGlyZXMiOjEzMDU3MjAwMDAsImlzc3VlZF9hdCI6MTMwNTcxMzEyMSwib2F1dGhfdG9rZW4iOiIxMzk1Nzc1MjI3NzE2NjN8Mi5BUUJFUXc4bE52RVpHcHhCLjM2MDAuMTMwNTcyMDAwMC4xLTEwMDAwMTMzMjI0MDgyN3xvaEYtUnpFQlI2dnlHS3dncGdqbnA1X1ZUNFkiLCJ1c2VyIjp7ImNvdW50cnkiOiJmciIsImxvY2FsZSI6ImZyX0ZSIiwiYWdlIjp7Im1pbiI6MjF9fSwidXNlcl9pZCI6IjEwMDAwMTMzMjI0MDgyNyJ9';
		protected static $INVALID_SIGNED_REQUEST = 'IGYtoeWnxtYa75UmzfegYb-dezdeeocYicSuCx39Tug.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImV4cGlyZXMiOjEzMDU3MjAwMDAsImlzc3VlZF9hdCI6MTMwNTcxMzEyMSwib2F1dGhfdG9rZW4iOiIxMzk1Nzc1MjI3NzE2NjN8Mi5BUUJFUXc4bE52RVpHcHhCLjM2MDAuMTMwNTcyMDAwMC4xLTEwMDAwMTMzMjI0MDgyN3xvaEYtUnpFQlI2dnlHS3dncGdqbnA1X1ZUNFkiLCJ1c2VyIjp7ImNvdW50cnkiOiJmciIsImxvY2FsZSI6ImZyX0ZSIiwiYWdlIjp7Im1pbiI6MjF9fSwidXNlcl9pZCI6IjEwMDAwMTMzMjI0MDgyNyJ9';
		
		
		protected $configuration;
		protected $facebook;
		
		protected $appId;
		protected $appSecret;
		protected $cookieSupport;
		protected $baseDomain;
		
		protected function setUp()
		{
			$this->appId 	 		= "139577522771663";
	        $this->appSecret 		= "6aa9b847f710b9223bad78713a5609c0";
		}
		
		protected function loadDefaultConfiguration()
		{
     		$this->configuration = new Configuration(array(
        		'appId' 		=> $this->appId,
        		'appSecret' 	=> $this->appSecret,
     			'debug'			=> true
        	));
    	
        	$requester = new CurlRequester();
        	$logger	   = new TestLogger();
        	$this->facebook = new Facebook($this->configuration, $requester, $logger);
		}
		
	
	}