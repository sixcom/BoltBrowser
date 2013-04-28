<?php 

require_once('BoltResponse.class.php');

class BoltBrowser
{
	protected 
		$_host = null,
		$_ch   = null;

	protected $_curl_options = array(
		CURLOPT_VERBOSE        => 1,
		CURLOPT_HEADER         => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.152 Safari/537.22',

	);

	public function __construct($host)
	{
		$this->_host = $host;
		$this->_ch   = curl_init();
		curl_setopt_array($this->_ch, $this->_curl_options);
	}

	public function setCookie($cookie)
	{
		curl_setopt($this->_ch, CURLOPT_COOKIE, $cookie);
	}

	public function get($url)
	{
		return $this->doRequest($url);
	}


	public function post($url, array $data)
	{
		curl_setopt($this->_ch, CURLOPT_POST, 1);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS,  http_build_query($data));
	}


	protected function doRequest($url)
	{
		curl_setopt($this->_ch, CURLOPT_URL, $this->_host . $url);
		$raw_response_data = curl_exec($this->_ch); 
		$response_meta     = curl_getinfo($this->_ch);

		return new BoltResponse($raw_response_data, $response_meta);
	}

}    