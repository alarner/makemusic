<?php
/* SVN FILE: $Id: app_controller.php 6305 2008-01-02 02:33:56Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 6305 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 21:33:56 -0500 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppController extends Controller {
 
 	var $helpers = array('Html', 'Javascript');  
    function checkSession()
    {
		// If the session info hasn't been set...
        if (!$this->Session->check('user'))
        {
            // Force the user to login
            $this->redirect('/users/login');
            exit();
        }
        else
        {
			$nowuser = $this->Session->read('user');
			$this->set('create', true);
			$this->set('has_artist', false);
			$this->set('has_location', false);
			$res_user = $this->User->findByEmailaddress($nowuser);
			$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
			$res_location = $this->Location->findByUser_id($res_user['User']['id']);
			$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.location_id' => $res_location['Location']['id'])));
			$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'])));

			$this->set('has_multiple_locations', false);                                        
			$criteria=$res_user['User']['id'];

			list($order,$limit,$page) = $this->Pagination->init($criteria);
			if ($res_user['User']['user_type'] == '180' && count($this->Location->findAllByUser_id($criteria, '', 'locationname ASC', $limit, $page)) > 1) {
				$this->set('has_multiple_locations', true);                                        
			}

			if ($res_user['User']['user_type'] == '220')
			{
				$this->layout = 'adminview';
			}
			if ($res_user['User']['user_type'] == '160')
			{
				$this->set('user_type', 'artist');
				if ($res_artist)
				{
					$this->set('Artist', $res_artist['Artist']);
					$this->set('has_artist', true);
					$this->set('has_location', false);
					$this->set('create', false);		
				}
			}
			if ($res_user['User']['user_type'] == '180')
			{
				$this->set('user_type', 'location');
				if ($res_location)
				{
					$this->set('Location', $res_location['Location']);
					$this->set('has_location', true);
					$this->set('has_artist', false);
					$this->set('create', false);
				}
			}
 
        }
    }
}
?>