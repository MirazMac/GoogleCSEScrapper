<?php

namespace MirazMac\GoogleCSE;

use MirazMac\GoogleCSE\Element\ElementBag;
use MirazMac\GoogleCSE\Element\ImageResultElement;
use MirazMac\GoogleCSE\Element\RichSnippetElement;
use MirazMac\GoogleCSE\Element\WebResultElement;
use \Requests_Cookie_Jar;
use \Requests_Session;

/**
 * Google CSE Scrapper
 *
 * @author  Miraz Mac <mirazmac@gmail.com>
 * @package MirazMac\GoogleCSE
 * @since 0.1
 */
class Scrapper
{
    /**
     * @var string Public URL template of Google CSE
     */
    const PUBLIC_CSE_URI     = 'https://cse.google.com/cse/publicurl?cx=%s';

    /**
     * @var string URL to Google CSE JavaScript
     */
    const CSE_JAVASCRIPT_URI = 'https://cse.google.com/cse/cse.js?hpg=1&cx=%s';

    /**
     * @var string Internal Google CSE API Key
     */
    const INTERNAL_API_KEY   = 'AIzaSyCVAXiUzRYsML1Pv6RwSG1gunmMikTzQqY';

    /**
     * @var string URL of the Google CSE Endpoint
     */
    const RESULTS_ENDPOINT   = 'https://cse.google.com/cse/element/v1';

    /**
     * @var string Path to CSE token storage
     */
    const CSE_TOKEN_PATH     = '/Storage/%s.php';

    /**
     * @var integer CSE token lifetime, in seconds
     */
    const CSE_TOKEN_LIFETIME = 86400;

    /**
     * Scrapper options
     *
     * @var array
     */
    protected $options = [];

    /**
     * CSE partner ID
     *
     * @var string
     */
    protected $partnerID;

    /**
     * Requests http session
     *
     * @var object
     */
    protected static $httpSession;

    /**
     * Create a new instance
     *
     * @param string $partnerID CSE partner ID
     * @param array  $options   Scrapper options
     */
    public function __construct($partnerID, array $options = [])
    {
        $this->partnerID = $partnerID;
        $this->setUpHttpSession();

        $cseToken = $this->parseJavaScript();

        $defaults = [
            'cse_token'   => $cseToken,
            'google_host' => 'www.google.com',
            'hl'          => 'en'
        ];
        $this->options = array_merge($defaults, $options);
    }

    /**
     * Perform a web search via Google CSE
     *
     * @param  string  $q     The search query
     * @param  integer $start Start position of the result
     * @param  integer $limit Results per page, currently supports 1-10
     * @param  array   $params Optional search parameters
     * @return object         If everything's allright, it will return an Element\ElementBag
     *
     * @throws \LogicException If response doesn't contain any results array
     */
    public function searchWeb($q, $start = 0, $limit = 10, array $params = [])
    {
        // Adjust parameters from arguments
        $params['num']        = $limit;
        $params['start']      = $start;

        // Perform the request
        $responseBody = $this->searchRequest($q, $params);

        // Make sure we have the results before continuing
        if (!isset($responseBody['results'])) {
            throw new \LogicException("Failed to find search results from the response!");
        }

        $parsedResults = [];
        foreach ($responseBody['results'] as $result) {
            // Add RichSnippet if its present
            if (!empty($result['richSnippet'])) {
                $result['richSnippet'] = new RichSnippetElement($result['richSnippet']);
            }
            // Create a new WebResultElement
            $parsedResults[] = new WebResultElement($result);
        }

        // Set cursor data if we've else empty array
        $cursorData = isset($responseBody['cursor']) ? $responseBody['cursor'] : [];
        // Set spelling data if we've else empty array
        $spellingData = isset($responseBody['spelling']) ? $responseBody['spelling'] : [];

        // Finally, pack everything up in a bag and return!
        return new ElementBag($parsedResults, $cursorData, $spellingData);
    }

    /**
     * Perform a image search via Google CSE
     *
     * @param  string  $q     The search query
     * @param  integer $start Start position of the result
     * @param  integer $limit Results per page, currently supports 1-20
     * @param  array   $params Optional search parameters
     * @return object         If everything's allright, it will return an Element\ElementBag
     *
     * @throws \LogicException If response doesn't contain any results array
     */
    public function searchImage($q, $start = 0, $limit = 20, array $params = [])
    {
        // Adjust parameters from arguments
        $params['num']        = $limit;
        $params['start']      = $start;
        $params['searchtype'] = 'image';

        // Perform the request
        $responseBody = $this->searchRequest($q, $params);

        // Make sure we have the results before continuing
        if (!isset($responseBody['results'])) {
            throw new \LogicException("Failed to find search results from the response!");
        }

        $parsedResults = [];
        foreach ($responseBody['results'] as $result) {
            // Create a new ImageResultElement
            $parsedResults[] = new ImageResultElement($result);
        }

        // Set cursor data if we've else empty array
        $cursorData = isset($responseBody['cursor']) ? $responseBody['cursor'] : [];
        // Set spelling data if we've else empty array
        $spellingData = isset($responseBody['spelling']) ? $responseBody['spelling'] : [];

        // Finally, pack everything up in a bag and return!
        return new ElementBag($parsedResults, $cursorData, $spellingData);
    }

    /**
     * Perform search request to the Google CSE endpoint
     *
     * @access protected
     * @param  string  $q      The search query
     * @param  array   $params Optional search parameters
     * @return array           If everything is successful, will return an array
     *                         with the response data
     */
    protected function searchRequest($q, array $params = [])
    {
        // Default parameters
        $defaults = [
            'key'         => static::INTERNAL_API_KEY,
            'rsz'         => 'filtered_cse',
            'q'           => $q,
            'oq'          => $q,
            'start'       => 0,
            'num'         => 10,
            'cx'          => $this->partnerID,
            'cse_tok'     => $this->options['cse_token'],
            'googlehost'  => $this->options['google_host'],
            'source'      => 'gcsc',
            'gss'         => '.com',
            'prettyPrint' => false,
            'nocache'     => time(),
            'hl'          => $this->options['hl'],
            'callback'    => 'google.search.cse.api' . rand(800, 2000),
        ];

        // Merge the parameters
        $params   = array_merge($defaults, $params);
        // Build the query
        $query    = http_build_query($params);
        // Adjust endpoint URL
        $endpoint = static::RESULTS_ENDPOINT . "?{$query}";
        // Perform the request
        $http     = static::$httpSession;
        $request  = $http->get($endpoint);

        $body = $request->body;

        // @see https://stackoverflow.com/a/5081588/10594860
        $jsonp = function ($jsonp, $assoc = false) {
            if ($jsonp[0] !== '[' && $jsonp[0] !== '{') {
                $jsonp = substr($jsonp, strpos($jsonp, '('));
            }
            return json_decode(trim($jsonp, '();'), $assoc);
        };

        $body = $jsonp($body, true);

        // Finally return the data, phew that was fun xD
        return $body;
    }

    /**
     * Parse the CSE JavaScript file to fetch the CSE token
     *
     * @access protected
     * @return string Possibly the CSE token
     *
     * @throws \RuntimeException If the JavaScript file isn't parse-able
     * @throws \RuntimeException If failed to store the CSE token via file-system
     */
    protected function parseJavaScript()
    {
        // Path to the CSE token file
        $cseTokenPath = __DIR__ . sprintf(static::CSE_TOKEN_PATH, md5($this->partnerID));
        // Cache file exists!
        if (is_file($cseTokenPath)) {
            // The token has expired
            if (time() - filemtime($cseTokenPath) >= static::CSE_TOKEN_LIFETIME) {
                // Delete the file
                @unlink($cseTokenPath);
            } else {
                // Otherwise use the cached token
                return require($cseTokenPath);
            }
        }

        // Perform a request to download the JS file
        $http    = static::$httpSession;
        $uri     = sprintf(static::CSE_JAVASCRIPT_URI, $this->partnerID);
        $request = $http->get($uri);
        // There we have our JS file
        $rawJS   = $request->body;

        // Try to match via a dirty but quick RegEx
        preg_match('/"cse_token":\s?"(.*)",/m', $rawJS, $tokenMatch);
        // Damn! We don't have it?
        if (!isset($tokenMatch[1])) {
            throw new \RuntimeException("Failed to parse CSE Javascript!");
        }

        // Store as a PHP file, because OpCache is the new cool!
        $tokenData = "<?php\nreturn ". "'{$tokenMatch[1]}';";
        // Make sure we save it without any problem, watson!
        if (!@file_put_contents($cseTokenPath, $tokenData)) {
            throw new \RuntimeException("Failed to store CSE token. Please check your file-system permissions!");
        }

        // There you have it, your own CSE token
        return $tokenMatch[1];
    }

    /**
     * Setup HTTP request session
     *
     * @return void
     */
    protected function setUpHttpSession()
    {
        // We're done already!
        if (static::$httpSession instanceof Requests_Session) {
            return;
        }

        // Eww, I hate PSR-0
        static::$httpSession = new Requests_Session;

        // Try client's user agent if present
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            // Maybe Google would bless us, if we use chrome? idk xD
            $useragent =
            'Mozilla/5.0 (Windows NT 6.3; WOW64) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Chrome/60.0.2214.115 Safari/537.36';
        }

        static::$httpSession->useragent = $useragent;
        // Typical accept headers
        static::$httpSession->headers['Accept'] =
        'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        static::$httpSession->headers['Accept-Language'] = 'en-US,en;q=0.5';
        // Obvious -_-
        static::$httpSession->headers['Referer'] = sprintf(static::PUBLIC_CSE_URI, $this->partnerID);
        // who am i kidding? lol xD
        static::$httpSession->headers['DNT'] = '1';
        // Not works, still will try
        if (isset($_SERVER['REMOTE_ADDR'])) {
            static::$httpSession->headers['X-Forwarded-For'] = $_SERVER['REMOTE_ADDR'];
        }
        // Time-out
        static::$httpSession->options['timeout'] = 50;
        static::$httpSession->options['connect_timeout'] = 50;
        // Probably this will make us look like more less of an asshole to Google
        static::$httpSession->options['cookies'] = new Requests_Cookie_Jar;
    }
}
