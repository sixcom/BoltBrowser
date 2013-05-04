<?php 
class BoltJSONPResponse extends BoltResponse 
{ 
  protected function _init()
  {
    parent::_init();
    $this->_formatted_data = json_decode(substr($this->_body, 3, -1), true);
  }
}