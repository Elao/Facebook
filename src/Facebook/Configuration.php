<?php

/*
 * This file is part of the Facebook SDK package.
 *
 * (c) Elao (http://www.elao.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Facebook;

use Facebook\Loader\CookieLoader;
use Facebook\Loader\SessionObjectLoader;
use Facebook\Loader\SignedRequestLoader;

use Facebook\Exception\ConfigurationException;

/**
 * Facebook Configuration store facebook related configuration options
 * Available options are:
 * appId:				*required* application Id
 * appSecret:			*required* application secret
 * appFacebookUrl:		the application facebook url for the canvas page ex: my-test-app for http://apps.facebook.com/my-test-app/ canvas page url
 * debug:				default false : is debug mode activated ?
 * permissions:			needed permissions to access your app
 * csrfProtection:		default true : activate or not the csrf protection via cookie
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class Configuration
{
    protected $appId;			 // Application ID
    protected $appSecret;		 // Application Secret
    protected $appFacebookUrl;           // Facebook base app url
    protected $appPermissions;           // Required permissions to generate login url
        
    protected $subscriptionsVerifyToken; // Required to use subscriptions manager   
    protected $subscriptionsCallback;    // Subscription callback
    
    protected $debug;			 // Debug mode
    protected $configuration;	 // Handle all params
  	
    public function __construct(array $configuration) {
		
        if (isset($configuration['appId'])) {
            $this->setAppId($configuration['appId']);
	}else{
            throw new ConfigurationException("You must define a appId configuration parameter");
	}
		
	if (isset($configuration['appSecret'])) {
            $this->setAppSecret($configuration['appSecret']);
	}else{
            throw new ConfigurationException("You must define a appSecret configuration parameter");
	}
		
	if (isset($configuration['appFacebookUrl'])) {
            $this->setAppFacebookUrl($configuration['appFacebookUrl']);
	}
		
	if (isset($configuration['permissions'])) {
            $this->setAppPermissions($configuration['permissions']);
	}
		
        if (isset($configuration['subscriptionsVerifyToken'])) {
            $this->setSubscriptionsVerifyToken($configuration['subscriptionsVerifyToken']);
        }
        
        if (isset($configuration['subscriptionsCallback'])) {
            $this->setSubscriptionsCallback($configuration['subscriptionsCallback']);
        }
        
	$this->configuration = $configuration; 
    }	
	
    public function get($key) {
        
        if (isset($this->configuration[$key])){
            return $this->configuration[$key];
	}else{
            return null;
        }
    }
	
    /**
     * Set the Application ID.
     * @param String $appId the Application ID
     */
    public function setAppId($appId) {
        
        $this->appId = $appId;
    }

    /**
     * Get the Application ID.
     * @return String the Application ID
     */
    public function getAppId() {
        
        return $this->appId;
    }

    /**
     * Set the API Secret.
     * @param String $appId the API Secret
     */
    public function setAppSecret($appSecret) {
        
        $this->appSecret = $appSecret;
    }

    /**
     * Get the API Secret.
     * @return String the API Secret
     */
    public function getAppSecret() {
        
        return $this->appSecret;
    }

    /**
     * Set the facebook url on facebook
     * @param string $appFacebookUrl
     */
    public function setAppFacebookUrl($appFacebookUrl) {
        
        $this->appFacebookUrl = $appFacebookUrl;
    }

    /**
     * Get the facebook url on facebook
     * @param string $appFacebookUrl
     */
    public function getAppFacebookUrl() {
        
        return $this->appFacebookUrl;
    }

    public function setAppPermissions($permissions) {
        
        $this->appPermissions = $permissions;
    }

    public function getAppPermissions() {
        
        return $this->appPermissions ? : array();
    }
    
    public function getSubscriptionsVerifyToken() {
        return $this->subscriptionsVerifyToken;
    }
    
    public function setSubscriptionsVerifyToken($subscriptionsVerifyToken) {
        $this->subscriptionsVerifyToken = $subscriptionsVerifyToken;
    }
    
    public function getSubscriptionsCallback() {
        return $this->subscriptionsCallback;
    }
    
    public function setSubscriptionsCallback($subscriptionsCallback) {
        $this->subscriptionsCallback = $subscriptionsCallback;
    }
    
}
