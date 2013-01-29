<?php 
class Ownartist extends AppModel 
{ 
var $name = 'Ownartist';
//var $validate = array( 
//	'website' => VALID_URL,
//	); 
var $belongsTo = array ('User' => array( 'className' => 'User', 'conditions'=>'','order'=>'','foreignKey'=>'user_id'));
var $hasMany = array ('Performance' => array( 'className' => 'Performance','conditions'=>'','order'=>'','foreignKey'=>'location_id')); 
} 
?>