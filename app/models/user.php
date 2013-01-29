<?php 
class User extends AppModel 
	{ 
	var $name = 'User';
	var $validate = array( 
	'emailaddress' => VALID_EMAIL,
	'initial_interest' => VALID_NOT_EMPTY,
	'zip_code' => '/^[0-9]{4,6}$/',
	'password' => '/^.{6,10}$/',
	'user_type' => VALID_NOT_EMPTY,
	'first_name' => VALID_NOT_EMPTY,
	'last_name' => VALID_NOT_EMPTY,
	); 
	var $hasMany = array (
		'Artist' => array( 'className' => 'Artist','conditions'=>'','order'=>'','foreignKey'=>''), 
		'Location' => array( 'className' => 'Location','conditions'=>'','order'=>'','foreignKey'=>'')
	);
	//var $hasOne = 
//	function beforeSave()
//		{
//			if ($this->data['User']['password'])
//			{
//				$this->data['User']['password'] = md5($this->data['User']['password']);
//			}
//		return true;
//		}
	} 
?>