<?php 
/**
 * Response class
 */
namespace PandaBolt\BoltBrowser\Response;

class BoltResponse
{
  /**
   * vars
   */
  protected
    $_raw_response   = null, //Raw response from curl
    $_meta           = null, //Meta data from curl
    $_body           = null, //Response body
    $_status_code    = null, //Http response code
    $_header         = null, //Response headers
    $_formatted_data = null, //Formated response data
    $_all_cookies    = array() //Cookies
  ;

  /**
   * Constructor
   * @param string $raw_response 
   * @param array $meta         
   */
  public function __construct($raw_response, $meta)
  {
    $this->_raw_response = $raw_response;
    $this->_meta         = $meta;

    $this->_init();
  }

  /**
   * Set up cookie 
   * @param  array  $cookies 
   * @return 
   */
  public function loadCookies(array $cookies)
  {
    $this->_all_cookies = $cookies;
  }

  /**
   * Get Cookies
   * @param  string $name 
   * @return mixed       
   */
  public function getCookie($name)
  {
    if (array_key_exists($name, $this->_all_cookies)) {
      return $this->_all_cookies[$name];
    }
  }

  /**
   * Get HTTP status code
   * @return string
   */
  public function getStatusCode()
  {
    return $this->_meta['http_code'];
  }

  /**
   * Get Set-Cookie header value
   * @return array
   */
  public function getSetCookie()
  {
    if (array_key_exists('Set-Cookie', $this->_header)) {
      return (array) $this->_header['Set-Cookie'];
    } else {
      return array();
    }
  }

  /**
   * Get formated data
   * @return mixed
   */
  public function getFormattedData()
  {
    return $this->_formatted_data;
  }

  /**
   * Initialization
   */
  protected function _init()
  {
    $header  = trim(substr($this->_raw_response, 0, $this->_meta['header_size']));
    $this->_body  = $this->_formatted_data  = substr($this->_raw_response, $this->_meta['header_size']);
    $this->_header = http_parse_headers($header);
  }
}