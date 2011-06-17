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
    const FACEBOOK_URL	 	= "https://www.facebook.com/";
    const FACEBOOK_API_URL  = "https://graph.facebook.com/";
    const FACEBOOK_APPS_URL = "http://apps.facebook.com/";
	
    protected $requester;		// Requester object
    protected $logger;			// Logger
	
    protected $configuration;	// Facebook Application Configuration
	
    protected $session;			// The related session object
    
    // List of loaded loaders & dumpers
    protected $loaders  	   = array();
    protected $dumpers		   = array();
    
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
        $this->requester = $requester;
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

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
        return $this->getFacebookUrl('extern/login_status.php', array_merge($this->getLoginStatusUrlDefaultParameters(), $params)
        );
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
        $params['access_token'] = $this->getAccessToken();

        // Resolve path
        $path = $this->parsePath($path);

        // Build api
        $url = $this->getApiUrl($path, $params);

        // Send the request to the graph api
        $result = $this->getRequester()->request($url, $params);

        if ($result && is_array($result) && isset($result['error'])) {
            $e = new ApiException($result);

            switch ($e->getType()) {
                case 'OAuthException':
                case 'invalid_token':
                    throw new AuthException($e->getMessage() . " calling " . $this->getUrl() . " with params " . implode(',', $this->getParams()));
            }
            throw $e;
        }

        // Return either a Facebook Object or a Facebook Object Collection
        if (isset($result['data'])) {
            return new FacebookCollection($this, $result['data']);
        } else {
            return new FacebookObject($this, $result);
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

}
