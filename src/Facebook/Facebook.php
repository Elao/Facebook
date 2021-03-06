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

use Facebook\Loader\LoaderInterface;
use Facebook\Dumper\DumperInterface;

use Facebook\Exception\ApiException;
use Facebook\Exception\ConfigurationException;
use Facebook\Exception\AuthException;

use Facebook\Session;
use Facebook\Object\FacebookCollection;
use Facebook\Object\FacebookObject;

use Facebook\Requester\RequesterInterface;

use Facebook\Configuration;
/**
 * Facebook object is the main Facebook Service.
 * It handles the configuration, and the sessions managment
 * It use the session loaders to creation Session Object, 
 * and eventually store them using session dumpers
 * 
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class Facebook
{
    const FACEBOOK_URL	      = "https://www.facebook.com/";
    const FACEBOOK_API_URL    = "https://graph.facebook.com/";
    const FACEBOOK_APPS_URL   = "http://apps.facebook.com/";
    const FACEBOOK_QUERY_URL  = "https://api.facebook.com/";
    
    const TRANSACTION_LIMIT = 20;
    
    protected $requester;		// Requester object
    protected $logger;			// Logger
	
    protected $configuration;           // Facebook Application Configuration
	
    protected $session;			// The related session object
    
    // List of loaded loaders & dumpers
    protected $loaders  	   = array();
    protected $dumpers		   = array();
    
    protected $creditsRequestHandler;       // Credits Request Handler
    protected $subscriptionsRequestHandler; // Subscriptions Request Handler
    protected $subscriptionsManager;        // Subscriptions Manager
    
    protected $transaction;
    protected $transactionCalls;
    
    /**
     * List of query parameters that get automatically dropped when rebuilding
     * the current URL.
     */
    protected static $DROP_QUERY_PARAMS = array(
        'session',
        'signed_request',
        'state'
    );

    public function __construct(Configuration $configuration, RequesterInterface $requester, $logger = null) {
        $this->requester        = $requester;
        $this->logger           = $logger;
        $this->configuration    = $configuration;
        $this->transaction      = false;
        $this->transactionCalls = array();
    }

    /**
     * @return type Facebook\Configuration;
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    public function addLoader(LoaderInterface $loader) {
        $this->loaders[] = $loader;
    }

    public function addDumper(DumperInterface $dumper) {
        $this->dumpers[] = $dumper;
    }

    public function getAppId() {
        return $this->getConfiguration()->getAppId();
    }

    public function getAppSecret() {
        return $this->getConfiguration()->getAppSecret();
    }

    public function getLoaders() {
        return $this->loaders;
    }

    public function getDumpers() {
        return $this->dumpers;
    }

    public function getLogger() {
        return $this->logger;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }

    public function getRequester() {
        return $this->requester;
    }

    public function setRequester(RequesterInterface $requester) {
        $this->requester = $requester;
    }
  
    public function getCreditsRequestHandler() {
        return $this->creditsRequestHandler;
    }
    
    public function setCreditsRequestHandler($creditsRequestHandler) {
        $this->creditsRequestHandler = $creditsRequestHandler;
    }
    
    public function getSubscriptionsRequestHandler() {
        return $this->subscriptionsRequestHandler;
    }
    
    public function setSubscriptionsRequestHandler($subscriptionsRequestHandler) {
        $this->subscriptionsRequestHandler = $subscriptionsRequestHandler;
    }
    
    public function getSubscriptionsManager() {
        return $this->subscriptionsManager;
    }
    
    public function setSubscriptionsManager($subscriptionsManager) {
        $this->subscriptionsManager = $subscriptionsManager;
    }
    
    public function getTransaction() {
        return $this->transaction;
    }
    
    public function startTransaction() {
        $this->transaction = true;
    }
    
    public function addTransactionCall($method, $url, $params) {
        if (count($this->transactionCalls) >= self::TRANSACTION_LIMIT){
            throw new ConfigurationException("Transaction calls limit ".self::TRANSACTION_LIMIT." reached");
        }
        
        $call = array('method' => $method);
        if (isset($params['access_token'])){
            $call['access_token'] = $params['access_token'];
            // unset($params['access_token']); 
            // DO NOT UNSET The access token params MUST be passed as query params
        }
        
        if ($method == 'GET'){ // Params should be added to query string relative
            $call['relative_url'] = $url.'?'.http_build_query($params);
        }else{
            $call['relative_url'] = $url;
            $call['body']         = http_build_query($params);
        }
        
        $this->transactionCalls[] = $call;
    }
    
    public function addTransactionCallFql($url, $params) {
        if (count($this->transactionCalls) >= self::TRANSACTION_LIMIT){
            throw new ConfigurationException("Transaction calls limit ".self::TRANSACTION_LIMIT." reached");
        }
        
        $call = array('method' => 'GET');
        if (isset($params['access_token'])){
            $call['access_token'] = $params['access_token'];
            // unset($params['access_token']); 
            // DO NOT UNSET The access token params MUST be passed as query params
        }
        
        if (isset($params['query_name'])){
            $call['name'] = $params['query_name'];
            unset($params['query_name']);
        }
        
        if (isset($params['omit_response_on_success'])){
            $call['omit_response_on_success'] = $params['omit_response_on_success'];
            unset($params['omit_response_on_success']);
        }
        
        $call['relative_url'] = $url.'?'.http_build_query($params);
        
        $this->transactionCalls[] = $call;
    }
    
    public function getBatchTransactionParam() {
        return json_encode($this->transactionCalls);
    }
    
  /**
     *  Try to get a session object from either the specified $loaders or the defaults ones
     *  Dump the session to the specified $dumpers or the defaults ones
     */
    public function getSession($useLoaders = null, $useDumpers = null, $forceNew = false) {

        $session = null;
        $loaders = array();
        $dumpers = array();

        if ($this->session && !$forceNew) {
            return $this->session;
        }

        if ($useLoaders) {
            if (is_array($useLoaders)) {
                $loaders = $useLoaders;
            } else {
                $loaders = array($useLoaders);
            }
        } else {
            $loaders = $this->getLoaders();
        }

        if ($useDumpers) {
            if (is_array($useDumpers)) {
                $dumpers = $useDumpers;
            } else {
                $dumpers = array($useDumpers);
            }
        } else {
            $dumpers = $this->getDumpers();
        }

        foreach ($loaders as $loader) {
            if (!$loader instanceof LoaderInterface) {
                throw new ConfigurationException('A session loader must implement LoaderInterface');
            }

            $this->debug("Loading session using : " . get_class($loader));

            // Try to load the session from a loader
            if ($loader->support()) {
                $this->debug("Loader supported");
                $session = $loader->auth();
                if ($session) {
                    $this->debug("Loading Success : " . $session->getAccessToken());
                    break;
                }
            }
        }

        if ($session) {
            foreach ($dumpers as $dumper) {
                if (!$dumper instanceof DumperInterface) {
                    throw new ConfigurationException('A session dumper must implement DumperInterface');
                }
                $this->debug("Dumping session using : " . get_class($dumper));

                // Try to dump the session
                if ($dumper->dump($session)) {
                    // Stop the dumping process if dumper ask it
                    if ($dumper->breakOnSucess()) {
                        $this->debug("Dumping process end by break");
                        break;
                    }
                }
            }
        }

        if ($session) {
            $this->session = $session;
            return $this->session;
        } else {
            $this->session = null;
            throw new AuthException("Unable to load a session object");
        }
    }

    /**
     * Get the UID from the session.
     * @return String the UID if available
     */
    public function getUser() {
        return $this->getSession()->getUser();
    }

    /**
     * Gets a OAuth access token.
     * @return String the access token
     */
    public function getAccessToken() {
        if ($this->getSession()) {
            return $this->getSession()->getAccessToken();
        }
        return false;
    }

    public function getCanvasUrl() {
        return sprintf(self::FACEBOOK_APPS_URL . '%s/', $this->getConfiguration()->getAppFacebookUrl());
    }

    /**
     * From Facebook Official SDK
     * Get a Login URL for use with redirects. By default, full page redirect is
     * assumed. If you are using the generated URL with a window.open() call in
     * JavaScript, you can pass in display=popup as part of the $params.
     *
     * The parameters:
     * - next: the url to go to after a successful login
     * - cancel_url: the url to go to after the user cancels
     * - req_perms: comma separated list of requested extended perms
     * - display: can be "page" (default, full page) or "popup"
     *
     * @param Array $params provide custom parameters
     * @return String the URL for the login flow
     */
    public function getLoginUrl($params = array()) {
        return $this->getFacebookUrl('login.php', array_merge($this->getLoginUrlDefaultParameters(), $params));
    }

    protected function getLoginUrlDefaultParameters() {
        return array(
            'api_key' => $this->getConfiguration()->getAppId(),
            'cancel_url' => $this->getCurrentUrl(),
            'display' => 'page',
            'fbconnect' => 0,
            'next' => $this->getCurrentUrl(),
            'return_session' => 1,
            'session_version' => 3,
            'v' => '1.0',
            'canvas' => 1,
            'req_perms' => implode(',', $this->getConfiguration()->getAppPermissions())
        );
    }

    /**
     * Get a Logout URL suitable for use with redirects.
     *
     * The parameters:
     * - next: the url to go to after a successful logout
     *
     * @param Array $params provide custom parameters
     * @return String the URL for the logout flow
     */
    public function getLogoutUrl($params = array()) {
        return $this->getFacebookUrl('logout.php', array_merge($this->getLogoutUrlDefaultParameters(), $params));
    }

    protected function getLogoutUrlDefaultParameters() {
        return array(
            'next' => $this->getCurrentUrl(),
            'access_token' => $this->getAccessToken()
        );
    }

    /**
     * Get a login status URL to fetch the status from facebook.
     *
     * The parameters:
     * - ok_session: the URL to go to if a session is found
     * - no_session: the URL to go to if the user is not connected
     * - no_user: the URL to go to if the user is not signed into facebook
     *
     * @param Array $params provide custom parameters
     * @return String the URL for the logout flow
     */
    public function getLoginStatusUrl($params = array()) {
        return $this->getFacebookUrl('extern/login_status.php', array_merge($this->getLoginStatusUrlDefaultParameters(), $params));
    }

    protected function getLoginStatusUrlDefaultParameters() {
        return array(
            'api_key' => $this->getAppId(),
            'no_session' => $this->getCurrentUrl(),
            'no_user' => $this->getCurrentUrl(),
            'ok_session' => $this->getCurrentUrl(),
            'session_version' => 3,
        );
    }

    /**
     * Invoke the Graph API.
     *
     * @param  String $path the path (required)
     * @param  String $method the http method (default 'GET')
     * @param  Array $params the query/post data
     * @return the decoded response object
     * @throws FacebookApiException
     */
    public function api($path = null, $method = 'GET', $params = array()) {
        if (!$this->session) {
            $this->getSession();
        }

        // Method as parameter
        if (is_array($method) && empty($params)) {
            $params = $method;
            $method = 'GET';
        }

        // Add the method
        $params['method'] = $method;

        // Access Token
        if (!isset($params['access_token'])){
            $params['access_token'] = $this->getAccessToken();
        }
        
        // Resolve path
        $path = $this->parsePath($path);

        // Build api
        $url = $this->getApiUrl($path, $params);

        if ($this->getTransaction()){
             $this->addTransactionCall($method, $path, $params);  
            return $this;
        }else{
            // Send the request to the graph api
            $result = $this->getRequester()->request($url, $params);
            return $this->processResult($result);
        }
    }

    /**
     * Call the api and return the batch result
     * @return array of FacebookCollection, FacebookObject or ApiException
     */
    public function commit() {
        if (!$this->getTransaction()){
            return new ConfigurationException("Commit called outside transaction");
        }
        
        $params = array(
            'access_token' => $this->getAccessToken(),
            'method'       => 'POST',
            'batch'        => $this->getBatchTransactionParam()
        );
        
        $url     = self::FACEBOOK_API_URL;
        $results = $this->getRequester()->request($url, $params);
        
        if (isset($results['error'])){
            throw new ApiException($results);
        }
        
        $data    = array();
        foreach ($results as $result){
            if (isset($result['body'])){
                $body   = json_decode($result['body'], true);
                $data[] = $this->processResult($body);
            }else{
                $data[] = var_export($result, true);
            }
        }
        
        $this->transactionCalls = array();
        $this->transaction      = false;
        
        return $data;
    }
    
    /**
     * Send a FQL query to facebook, and return the result
     * @param string $fql The FQL query
     */
    public function query($fql, $params = array()) {
        
        if (!$this->session) {
            $this->getSession();
        }
        
        $path = 'method/fql.query';
        $params['format'] = 'JSON';
        $params['query']  = $fql;
        
        if (!isset($params['access_token'])){
            $params['access_token'] = $this->getAccessToken();
        }
        
        $path = $this->parsePath($path);
        $url  = $this->getFqlUrl($path, $params);
        
        if ($this->getTransaction()){
             $this->addTransactionCallFql($path, $params);
            return $this;
        }else{
            // Send the request to the graph api
            $result = $this->getRequester()->request($url, $params);
            return $this->processResult($result);
        }
    }
    
    /**
     * Process a facebook result (can be from api call, batch call, query call)
     * @param type $result
     * @return FacebookCollection or FacebookObject or throw Exception
     */
    protected function processResult($result) {
        if ($result && is_array($result) && (isset($result['error']) || isset($result['error_code']))) {
            $e = new ApiException($result);

            switch ($e->getType()) {
                case 'OAuthException':
                case 'invalid_token':
                    return new AuthException($e->getMessage() . " calling " . $this->getUrl());
            }
            return new \Exception($e->getMessage());
        }

        // Return either a Facebook Object or a Facebook Object Collection
        if (is_array($result) && isset($result['data'])) {
            return new FacebookCollection($this, $result['data']);
        } elseif (is_array($result) && isset($result['id'])) {
            return new FacebookObject($this, $result);
        } else {
            return $result; // Unexpected result ...
        }
    }
    
    /**
     * Parse the path to replace token by related configuration entries
     * @param string $path
     * @return string
     */
    protected function parsePath($path) {

        $path = str_replace('%app_id%', $this->getConfiguration()->getAppId(), $path);
        return $path;
    }

    /**
     * Return a facebook url with defined options
     */
    public function getFacebookUrl($path, $params) {
        return $this->getUrl(self::FACEBOOK_URL, $path, $params);
    }

    public function getApiUrl($path, $params) {
        return $this->getUrl(self::FACEBOOK_API_URL, $path, $params);
    }

    public function getFqlUrl($path, $params) {
        return $this->getUrl(self::FACEBOOK_QUERY_URL, $path, $params);
    }
    /**
     * Build the URL for given domain alias, path and parameters.
     *
     * @param $name String the name of the domain
     * @param $path String optional path (without a leading slash)
     * @param $params Array optional query parameters
     * @return String the URL for the given parameters
     */
    protected function getUrl($url = self::FACEBOOK_URL, $path='', $params=array()) {
        if ($path) {
            if ($path[0] === '/') {
                $path = substr($path, 1);
            }
            $url .= $path;
        }
        if ($params) {
            $url .= '?' . http_build_query($params, null, '&');
        }
        return $url;
    }

    /**
     * Returns the Current URL, stripping it of known FB parameters that should
     * not persist.
     *
     * @return String the current URL
     */
    protected function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);

        // drop known fb params
        $query = '';
        if (!empty($parts['query'])) {
            $params = array();
            parse_str($parts['query'], $params);
            foreach (self::$DROP_QUERY_PARAMS as $key) {
                unset($params[$key]);
            }
            if (!empty($params)) {
                $query = '?' . http_build_query($params, null, '&');
            }
        }

        // use port if non default
        $port = isset($parts['port']) &&
                (($protocol === 'http://' && $parts['port'] !== 80) ||
                ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }

    public function debug($msg) {
        if ($this->getLogger() && method_exists($this->getLogger(), 'debug')) {
            $this->getLogger()->debug("[Facebook] $msg");
        } else {
            echo $msg . "\n";
        }
    }

    /**
     * Base64 encoding that doesn't need to be urlencode()ed.
     * Exactly the same as base64_encode except it uses
     *   - instead of +
     *   _ instead of /
     *
     * @param String base64UrlEncodeded string
     */
    public function base64UrlDecode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    /**
     * Parses a signed_request and validates the signature.
     * Then saves it in $this->signed_data
     *
     * @param String A signed token
     * @return Array the payload inside it or null if the sig is wrong
     */
    public function parseSignedRequest($signed_request) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $sig = $this->base64UrlDecode($encoded_sig);
        $data = json_decode($this->base64UrlDecode($payload), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            throw new AuthException('Algorithm missmatch ' . $data['algorithm']);
        }

        // Check signature
        $expected_sig = hash_hmac('sha256', $payload, $this->getAppSecret(), $raw = true);

        if ($sig !== $expected_sig) {
            throw new AuthException('Invalid signed Request sig ' . $sig . ' vs ' . $expected_sig);
        }

        // Check expiration
        return $data;
    }
}
