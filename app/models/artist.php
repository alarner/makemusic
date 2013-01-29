<?php 
class Artist extends AppModel 
{ 
var $name = 'Artist'; 

//var $validate = array(
//		'Artist/groupname' =>  'notEmpty'
//);
var $validate = array(
		'groupname' => VALID_NOT_EMPTY
);


var $belongsTo = array ('User' => array( 'className' => 'User', 'conditions'=>'','order'=>'','foreignKey'=>'user_id'));
var $hasMany = array ('Performance' => array( 'className' => 'Performance','conditions'=>'','order'=>'','foreignKey'=>'artist_id')); 
var $data = array('');
} 
?>