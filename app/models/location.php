<?php 
class Location extends AppModel 
{ 
var $name = 'Location';
//var $validate = array( 
//	'website' => VALID_URL,
//	);                
var $validate = array(
		'type' => '/.{3}+/',
		'locationname' => VALID_NOT_EMPTY,
		//'hood' => VALID_NOT_EMPTY,
		'street_add1' => VALID_NOT_EMPTY,
		'city' => VALID_NOT_EMPTY,
		'zip_code' => VALID_NOT_EMPTY,
		'phpri1' => VALID_NOT_EMPTY,         
		'phpri2' => VALID_NOT_EMPTY,         
		'phpri3' => VALID_NOT_EMPTY         
);
	
var $belongsTo = array ('User' => array( 'className' => 'User', 'conditions'=>'','order'=>'','foreignKey'=>'user_id'));
var $hasMany = array ('Performance' => array( 'className' => 'Performance','conditions'=>'','order'=>'','foreignKey'=>'artist_id')); 
} 
?>