<?php 
class Ownlocation extends AppModel 
{ 
var $name = 'Ownlocation';
//var $validate = array( 
//	'website' => VALID_URL,
//	); 
var $belongsTo = array ('User' => array( 'className' => 'User', 'conditions'=>'','order'=>'','foreignKey'=>'user_id'));
var $hasMany = array ('Performance' => array( 'className' => 'Performance','conditions'=>'','order'=>'','foreignKey'=>'artist_id')); 
} 
?>