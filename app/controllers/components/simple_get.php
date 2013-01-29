<?php
/**
 * SimpleGet is a component that manipulates the pass in the params array. It works much 
 * like the GET. I can better explain it thru an example. Let's say we have the ffg url: 
 * http://controller/action?var1=value. Using the component, we can rewrite it as 
 * http://controller/action/var1/value. Then in the controller, we can ask for var1's value 
 * easily.
 *
 * @version 1.1.3.2967
 */
uses('sanitize');
class SimpleGetComponent extends Object 
{
	
	/**
	 * The actual $this->params['data']
	 *
	 * @var unknown_type
	 */
	var $_pass = array();
	
	var $_controller;
	var $_passIsSet;
	var $_sanitize;
	
	
	function __construct() {
		parent::__construct();
		$this->sanitize = &new Sanitize;
	}
	
	function startup(&$controller) {
		
		if (!$this->isEmpty($controller->params)) {
			$this->_pass = $controller->params['pass'];
		}
		
	}
	
	/**
	 * Checks if the $this->params['pass'] is set and not empty
	 *
	 * @return boolean
	 */
	function isEmpty($tmp) {
		if (isset($tmp['pass']) and count($tmp['pass']) > 0) {
			return false;
		}
		return true;
	}
	
	/**
	 * For public access
	 * @param array $params
	 */
	function init($params) {
		
		$this->_pass = array();
		if (!$this->isEmpty($params)) {
			$this->_pass = $params['pass'];
		} 
		
	}
	
	/**
	 * Returns the next designated value of $toSearch in the $this->params['pass']
	 *
	 * @param string $toSearch
	 * @return mixed
	 */
	function getValueOf($toSearch) {		
		
		if (in_array($toSearch, $this->_pass)) {
			$key = array_keys($this->_pass, $toSearch);
			//return $this->_pass[$key[0]+1];
			return $this->sanitize->paranoid($this->_pass[$key[0]+1]);
		}
		return false;		
	}
	
	/**No use as of now
	 * List parameters into an array with key as the general name.
	 * The parameters should follow the /name-of-value1/value1/name-of-value2/value2
	 *
	 * @param array $params
	 * @return array
	 */
	/*function listParams($params) {
		$this->givenPass = $params;
		if ($this->isEmpty()) {
			debug('its empty');
			return false;
		}
		$pass = $this->givenPass['pass'];
		foreach ($pass as $key=>$value) {
			if (!is_numeric($key)) {
				continue;
			}
			if ($key % 2 == 0) {
				$list[$value] = isset($pass[$key+1]) ? $pass[$key+1] : '';
			}
		}
		return $list;
	}*/
		
}
?>