<?php 
class BoltResponse
{
	protected 
		$_raw_response = null,
		$_meta         = null,
		$_body         = null,
    $_status_code  = null,
		$_header       = null;  

	public function __construct($raw_response, $meta)
	{
		$this->_raw_response = $raw_response;
		$this->_meta         = $meta;

		$this->_init();
	}

  public function getStatusCode()
  {
    return $this->_meta['http_code'];
  }

  public function getSetCookie()
  {
    if (array_key_exists('Set-Cookie', $this->_header)) {
      return (array) $this->_header['Set-Cookie'];
    } else {
      return array();
    }
  }


	protected function _init()
	{
		$header  = trim(substr($this->_raw_response, 0, $this->_meta['header_size']));
		$this->_body    = substr($this->_raw_response, $this->_meta['header_size']);
		$this->_header = http_parse_headers($header);
	}
}