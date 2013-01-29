<?php 
class Performance extends AppModel 
{ 
var $name = 'Performance'; 
//var $belongsTo =array(
//					array ('Artist' => array( 'className' => 'Artist', 'conditions'=>'','order'=>'','foreignKey'=>'artist_id')),
//					array ('Location' => array( 'className' => 'Location', 'conditions'=>'','order'=>'','foreignKey'=>'location_id'))
//					);
//var $belongsTo = ;
    var $belongsTo = array('Artist' =>
                           array('className'  => 'Artist',
                                 'conditions' => '',
                                 'order'      => '',
                                 'foreignKey' => 'artist_id'
                           ),
                           'Location' =>
                           array('className'  => 'Location',
                                 'conditions' => '',
                                 'order'      => '',
                                 'foreignKey' => 'location_id'
                           )
                     );
					
} 
?>