<?php 
/**
 * Core class for handling all browser behavior 
 */

namespace PandaBolt\BoltBrowser;

use PandaBolt\BoltBrowser\Response\BoltResponse;
use PandaBolt\BoltBrowser\Response\BoltJSONPResponse;
use PandaBolt\BoltBrowser\Cookie\BoltFilePersistentCookieManager;

class BoltBrowser
{
  /**
   * vars
   */
	protected 
    $_cookie_file_name = null, //File path to store the cookies
    $_cookie_manager   = null, //Cookie manger object
		$_host             = null, //Host name for the request url
		$_ch               = null, //Curl handler
    $_response_class   = 'PandaBolt\BoltBrowser\Response\BoltResponse'; //Default response class

  /**
   * Default curl parameters
   * @var array
   */
	protected $_curl_options = array(
		//CURLOPT_VERBOSE        => 1,
		CURLOPT_HEADER         => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.152 Safari/537.22',
	);

  /**
   * Constructor
   * 
   * @param string $host             
   * @param string $cookie_file_name
   */
	public function __construct($host, $cookie_file_name = null)
	{
		$this->_host             = $host;
    $this->_cookie_file_name = $cookie_file_name;
		$this->_ch               = curl_init();
		curl_setopt_array($this->_ch, $this->_curl_options);
	}

  /**
   * Get cookie file name
   * 
   * @return string
   */
  public function getCookieFileName()
  {
    if (!$this->_cookie_file_name) {
      $this->_cookie_file_name = $this->getHostName();
    }

    return $this->_cookie_file_name;
  }

  /**
   * Get host name
   * 
   * @return string
   */
  public function getHostName()
  {
    return parse_url($this->_host, PHP_URL_HOST);
  }

  /**
   * Register the cookie manager class
   * @param  string $cookie_manager_class_name 
   */
  public function registerCookieManagerClass($cookie_manager_class_name) 
  {
    $this->_cookie_manager = new $cookie_manager_class_name($this->getCookieFileName());
  }

  /**
   * Register response class
   * @param  string $response_class_name                  
   */
  public function registerResponseClass($response_class_name) 
  {
    $this->_response_class = $response_class_name;
  }

  /**
   * Set options for CURL
   * @param array $opts 
   */
  public function setOptions(array $opts) 
  {
    curl_setopt_array($this->_ch, $opts);
  }

  /**
   * Set cookies
   * @param string $cookie
   */
	public function setCookie($cookie)
	{
		curl_setopt($this->_ch, CURLOPT_COOKIE, $cookie);
	}

  /**
   * Make a get request
   * @param  string $url
   * @return Object -- registered response class object
   */
	public function get($url)
	{
		return $this->doRequest($url);
	}


  /**
   * Make a post request
   * @param  string $url
   * @param  array  $data
   * @return Object -- registered response class object
   */
	public function post($url, array $data)
	{
		curl_setopt($this->_ch, CURLOPT_POST, 1);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS,  http_build_query($data));

    return $this->doRequest($url);
	}

  /**
   * Download the content to a given file path
   * @param  string $url       
   * @param  string $file_path
   */
  public function download($url, $file_path)
  {
    set_time_limit(0);
    $fp = fopen ($file_path, 'w');

    $this->setOptions(array(
      CURLOPT_FILE           => $fp,
      CURLOPT_FOLLOWLOCATION => 1,
      CURLOPT_VERBOSE        => 0,
      CURLOPT_HEADER         => 0,
    ));
 
    $this->doRequest($url);

    fclose($fp);
  }

  /**
   * Make a http request
   * @param  string $url 
   * @return
   */
	protected function doRequest($url)
	{
    if (
      $this->_cookie_manager
      && ($cookie_string = $this->_cookie_manager->getCookieString())
    ) {
      curl_setopt($this->_ch, CURLOPT_COOKIE, $cookie_string);
    }

		curl_setopt($this->_ch, CURLOPT_URL, $this->_host . $url);
		$raw_response_data = curl_exec($this->_ch); 

		$response_meta     = curl_getinfo($this->_ch);

    $reflecion_class = new \ReflectionClass($this->_response_class);
		$response = $reflecion_class->newInstance($raw_response_data, $response_meta);

    if (
      ($response->getStatusCode() == 200)
       && $this->_cookie_manager
     ) {
      $this->_cookie_manager->setCookieString($response->getSetCookie());
    }

    if ($this->_cookie_manager) {
   
      $response->loadCookies($this->_cookie_manager->getCookieArray());
    }

    return $response;
	}

}    