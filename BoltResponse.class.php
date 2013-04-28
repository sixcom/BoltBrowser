<?php 
class BoltResponse
{
	protected 
		$_raw_response = null,
		$_meta         = null,
		$_body         = null,
		$_header       = null;  

	public function __construct($raw_response, $meta)
	{
		$this->_raw_response = $raw_response;
		$this->_meta         = $meta;

		$this->_init();
	}

	protected function _init()
	{
		$header  = trim(substr($this->_raw_response, 0, $this->_meta['header_size']));
		$body    = substr($this->_raw_response, $this->_meta['header_size']);

		$header = explode("\n", $header);

		$this->_header = array();
		foreach ($header as $hd) {
			if (($pos = strpos($hd, ':')) !== false) {

				$header_key = trim(substr($hd, 0, $pos));
				if ($header_key == 'Set-Cookie') {
					$this->_header[$header_key][] = trim(substr($hd, $pos + 1));
				} else {
					$this->_header[$header_key] = trim(substr($hd, $pos + 1));
				}
				
			}
		}

		var_dump($this->_header);
	}
}