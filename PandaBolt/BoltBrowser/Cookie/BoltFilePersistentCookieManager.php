<?php
/**
 * Cookie manager class for handling the cookie storage and retrieval
 */
namespace PandaBolt\BoltBrowser\Cookie;

class BoltFilePersistentCookieManager
{
  /*
   * vars
   */
  protected 
    $_host            = null, //Host name
    $_file_dir        = null, //File directory path
    $_file_path       = null, //Full file path
    $_cookie_string   = null; //Cookie content

  /**
   * Constructor
   * @param string $host     
   * @param string $file_dir
   */
  public function __construct($host, $file_dir = '/tmp/')
  {
    $this->_host           = $host;
    $this->_file_dir       = '/tmp/';
    $this->_file_path      = '/tmp/' . 'BPCM-'  . $host;
  }

  /**
   * Convert cookie string to an array
   * @return array
   */
  public function getCookieArray()
  {
    $cookie_string = $this->getCookieString();
    $cookie_array  = array(); 
  
    parse_str(str_replace(';', '&', $cookie_string), $cookie_array);

    return $cookie_array;

  }

  /**
   * Get cookie string
   * @return string
   */
  public function getCookieString()
  {
    if (is_null($this->_cookie_string)) {
      if (is_file($this->_file_path)) {
        $file = new \SplFileObject($this->_file_path);
        $content = array();
        while (!$file->eof()) {
          $content[] = $file->fgets();
        }
        $this->_cookie_string = implode("\n", $content);
      } else {
        $this->_cookie_string = '';
      }
    }

    return $this->_cookie_string;
  }

  /**
   * Set cookie string
   * @param array $new_cookies_raw 
   */
  public function setCookieString(array $new_cookies_raw)
  {
    if (empty($new_cookies_raw)) {
      return;
    }

    $cookie_array = $this->getCookieArray();

    foreach ($new_cookies_raw as $new_cookie_raw) {
      $new_cookie_object = http_parse_cookie($new_cookie_raw);
      foreach ($new_cookie_object->cookies as $cookie_var => $cookie_val) {
        $cookie_array[$cookie_var] = $cookie_val;
      }
    }

    $new_cookie_array = array();
    foreach($cookie_array as $cookie_var => $cookie_val) {
      $new_cookie_array[] = $cookie_var . '=' . $cookie_val;
    }

    $file = new \SplFileObject($this->_file_path, 'w');
    $file->fwrite(implode(';', $new_cookie_array));
    
    $this->_cookie_string = null;
  }
}