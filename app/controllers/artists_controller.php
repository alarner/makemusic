<?php
class ArtistsController extends AppController
{

	var $name = 'Artists';
	var $uses = array('Artist', 'User','Performance','Location');
	var $components = array('SimpleGet', 'Mailer', 'Pagination');
	var $helpers = array('Pagination', 'Javascript');
	var $layout = 'artists';
	var $emailClose = "\n\nThank You,\nMake Music New York 2012";
    var $locations = array();
function create()
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		
		$this->pageTitle = 'Create';
		if (!empty($this->data))
		{
			if ($this->Artist->validates($this->data))
			{
				$nowuser = $this->Session->read('user'); 
				if ($nowuser) 
				{ 
					$res_user = $this->User->findByEmailaddress($nowuser); 
					$this->set('User', $res_user['User']);
					if ($this->Artist->findByUser_id($res_user['User']['id']))
					{
						$this->Session->setFlash('<div class="error">You have already created an artist.<br><a href="/newyork/makemusicny/locations">Click Here to find Locations to perform at</a></div><br>'); 
  						$this->redirect('/users/artist');  
					}
					else
					{
						if ($res_user['User']['user_type'] == '180')
						{
							$this->Session->setFlash('<div class="notice">You registered as a location</div><br>'); 
  							$this->redirect('/users');  
						}
						else
						{
							if ($this->Artist->findByGroupname($this->data['Artist']['groupname']))
							{
								$this->Artist->invalidate('groupname');
								$this->set('groupname_error', '<div class="error">Name has already been created</div><br>');
							}
							else
							{                         
								if (!empty($this->data['Artist']['groupname'])) {								
									$userid = $res_user['User']['id'];
									$this->data['Artist']['user_id'] = $userid;
									$genre1 = $this->data['Artist']['genre1'];
									$genre2 = $this->data['Artist']['genre2'];
									$genre3 = $this->data['Artist']['genre3'];
									$this->data['Artist'][$genre1] = "1";
									$this->data['Artist'][$genre2] = "1";
									$this->data['Artist'][$genre3] = "1"; 
									if (!empty($this->data['Artist']['website'])) {
										$this->data['Artist']['website'] = (stristr($this->data['Artist']['website'], "http://")) ? $this->data['Artist']['website']: "http://".$this->data['Artist']['website'];
							    	}
									if (!empty($this->data['Artist']['myspace'])) {
								 		$this->data['Artist']['myspace'] = (stristr($this->data['Artist']['myspace'], "http://")) ? $this->data['Artist']['myspace']: "http://".$this->data['Artist']['myspace'];
                                    }
									if (empty($genre1) && empty($genre2) && empty($genre3))
									{
										$this->data['Artist']['other'] = "1";
									}
									if ($this->Artist->save($this->data))
									{
										$this->Session->setFlash('<div class="success">Your Artist has been saved. You now have the following options:<br><a href="/participate/locations/">Find a performance location</a><br><a href="/participate/users/ownlocation">Create your own performance location</a></div><br>');
	  									$this->redirect('/users/artist');
									}
									else
									{
										$this->Session->setFlash('<div class="error">The Artist could not be saved. Please, try again.</div><br>');
									}  
    							} else {
									$this->Artist->invalidate('groupname');
 									$this->set('groupname_error', '<div class="error">Name has already been created</div><br>');
   								}
							}
						}
					}
				}
				else
				{
					$this->redirect('/users');
				}
			}
			else
			{
		   		$this->Session->setFlash('<div class="error">The Artist could not be saved. Please, try again.</div><br>');
			}
		}
	}
function edit($id = null)
	{ 
  		$this->layout = 'myaccount';
  		$this->checkSession();
		$this->pageTitle = 'Edit';
		$nowuser = $this->Session->read('user');
		$res_user = $this->User->findByEmailaddress($nowuser);
		$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Invalid Artist</div><br>'); 
  			$this->redirect('/users'); 
 		} 
 		if ($id = $res_artist['Artist']['id'])
   		{ 		
	 		if (!empty($this->data))
   			{
				if ($this->Artist->validates($this->data))
				{
	   				$this->cleanUpFields(); 
	   				$res_artist['Artist']['groupname'] =  $this->data['Artist']['groupname'];
					$res_artist['Artist']['website'] =  $this->data['Artist']['website'];
					$res_artist['Artist']['myspace'] =  $this->data['Artist']['myspace'];
					$res_artist['Artist']['previousVenues'] =  $this->data['Artist']['previousVenues'];

					$res_artist['Artist']['blues'] =  "0";
					$res_artist['Artist']['cabaret'] =  "0";
					$res_artist['Artist']['classical'] =  "0";
					$res_artist['Artist']['country'] =  "0";
					$res_artist['Artist']['electronic'] =  "0";
					$res_artist['Artist']['experimental'] =  "0";
					$res_artist['Artist']['folk'] =  "0";
					$res_artist['Artist']['hiphop'] =  "0";
					$res_artist['Artist']['jazz'] =  "0";
					$res_artist['Artist']['kids'] =  "0";
					$res_artist['Artist']['latin'] =  "0";
					$res_artist['Artist']['opera'] =  "0";
					$res_artist['Artist']['pop'] =  "0";
					$res_artist['Artist']['reggae'] =  "0";
					$res_artist['Artist']['religious'] =  "0";
					$res_artist['Artist']['rock'] =  "0";
					$res_artist['Artist']['soul'] =  "0";
					$res_artist['Artist']['standards'] =  "0";
					$res_artist['Artist']['world'] =  "0";
					$res_artist['Artist']['other'] =  "0";
				
					$res_artist['Artist']['genre1'] = $this->data['Artist']['genre1'];
					$res_artist['Artist']['genre2'] = $this->data['Artist']['genre2'];
					$res_artist['Artist']['genre3'] = $this->data['Artist']['genre3'];
				
					$res_artist['Artist']['description'] =  $this->data['Artist']['description'];
					$res_artist['Artist']['promote'] =  $this->data['Artist']['promote'];
					$res_artist['Artist']['electricity'] =  $this->data['Artist']['electricity'];
					$res_artist['Artist']['time_midday'] =  $this->data['Artist']['time_midday'];
					$res_artist['Artist']['time_afternoon'] =  $this->data['Artist']['time_afternoon'];
					$res_artist['Artist']['time_lateafter'] =  $this->data['Artist']['time_lateafter'];
					$res_artist['Artist']['time_evening'] =  $this->data['Artist']['time_evening'];
				
					$genre1 = $this->data['Artist']['genre1'];
					$genre2 = $this->data['Artist']['genre2'];
					$genre3 = $this->data['Artist']['genre3'];
					$res_artist['Artist'][$genre1] = "1";
					$res_artist['Artist'][$genre2] = "1";
					$res_artist['Artist'][$genre3] = "1";
					if (!empty($this->data['Artist']['website'])) {
						$res_artist['Artist']['website'] = (stristr($this->data['Artist']['website'], "http://")) ? $this->data['Artist']['website']: "http://".$this->data['Artist']['website'];
			    	}
					if (!empty($this->data['Artist']['myspace'])) {
				 		$res_artist['Artist']['myspace'] = (stristr($this->data['Artist']['myspace'], "http://")) ? $this->data['Artist']['myspace']: "http://".$this->data['Artist']['myspace'];
                    }
					if (empty($genre1) && empty($genre2) && empty($genre3))
					{
						$res_artist['Artist']['other'] = "1";
					}
					$this->set('Artist', $res_artist['Artist']);
					if ($this->Artist->save($res_artist['Artist'], false))
	   				{ 
	    				$this->Session->setFlash('<div class="success">Artist Information Updated</div><br>'); 
		    			$this->redirect('/users/artist'); 
	  				}
	  				 else
	  				{ 
	    				$this->Session->setFlash('<div class="error">Artist information could not be updated - Please try again later</div><br>'); 
	   				} 
	  			} 
	  		}
			if (empty($this->data))
  			{ 
   				$this->data = $this->Artist->read(null, $id);
  			}
   		}
   		else
   		{
   		   	$this->Session->setFlash('<div class="error">Invalid Artist</div><br>'); 
  			$this->redirect('/users');  
   		}
	}
function index() 
	{	 
		$this->checkSession();
		
		$criteria=NULL;
		//$order = 'groupname ASC';
		$this->Pagination->show = 20;
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Artist->findAll($criteria, '', 'groupname ASC', $limit, $page);
		$this->set('data',$data); 
		//$this->set('knownartists',$this->Artist->findAll('','','groupname ASC')); 
	}
function sort()
	{
		$this->checkSession();
		
		$this->Pagination->show = 20;
		$genre = $this->SimpleGet->getValueOf('genres');
		$artistgenre = "Artist.".$genre;
		$criteria=array($artistgenre => '1');
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Artist->findAll($criteria, '', 'groupname ASC', $limit, $page);
		$this->set('data',$data); 
		$this->set('genre', $genre);
		
		//$this->set('knownartists',$this->Artist->findAll(array($artistgenre => '1'),'','groupname ASC'));
	}
function view($id = null)
	{ 
		$this->checkSession();
		
		$nowuser = $this->Session->read('user');
		$this->pageTitle = 'Artist Information';
		$this->set('booked',false);
		$this->set('requested',false);
		if (!$id)
	  		{ 
   				$this->Session->setFlash('<div class="error">Invalid Artist.</div>'); 
   				$this->redirect('/artist');  
  			} 
  			$res_artist = $this->Artist->read(null, $id);
  			$thisuser = $this->User->findByEmailaddress($nowuser);
			$res_user = $this->User->findById($res_artist['Artist']['user_id']);
			$res_location = $this->Location->findByUser_id($thisuser['User']['id']);   
			$this->set('Artist', $res_artist['Artist']);
			$this->set('User', $res_user);
			$this->set('nowuser', $thisuser);
			$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'])));
			$booked = $this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.location_id' => $res_location['Location']['id']));
			$pending = $this->Performance->findAll(array("and" => array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.location_id' => $res_location['Location']['id']), "or" => array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')));
			if ($booked)
			{
				$this->set('booked',true);
			}
			if ($pending)
			{
				$this->set('requested',true);
			}
	}	
function book($id = null)
	{
		$this->checkSession();
		$this->pageTitle = 'Book Artist';
		$nowuser = $this->Session->read('user');
		$res_user = $this->User->findByEmailaddress($nowuser);
		$res_location = $this->Location->findByUser_id($res_user['User']['id']);  
		// print_r($this);
		//$this->ArtistsController->getLocationIDName($res_user['User']['id']);
//		$this->set('Locations',$this->locations($res_user['User']['id'])); 
		$this->set('Locations',$this->Location->query("select id, locationname from locations where user_id = ".$res_user['User']['id']." order by locationname" ) ); 
		//print_r($locations);
		$book_artist = $this->Artist->findById($id);
		$book_user = $this->User->findById($book_artist['Artist']['user_id']);
		$this->set('Location', $res_location['Location']);		
		$this->set('Artist', $book_artist['Artist']);
		$this->set('User',$book_user['User']);
		$email = $book_user['User']['emailaddress'];
		$fname = $book_user['User']['first_name'];
		$lname = $book_user['User']['last_name'];
		$fullname = $fname.' '.$lname;
		if (!$book_artist)
		{
  			$this->redirect('/users'); 			
		}
		if (!$res_location)
		{
			$this->redirect('/users');
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Invalid Artist</div><br>'); 
  			$this->redirect('/artists'); 
 		} 
 		if ($res_user['User']['user_type'] == '160')
		{
			$this->Session->setFlash('<div class="notice">You are an Artist. You must book a Location</div><br>'); 
  			$this->redirect('/locations');
		}
		if (!empty($this->data))
 		{
			$this->data['Performance']['location_id'] = $this->data['Performance']['location_id'];
			$this->data['Performance']['location_confirmed'] = '1';
			$this->data['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
			$this->data['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
			if ($this->Performance->save($this->data,false))
			{
 				$req_location = $this->Location->findById($this->data['Performance']['location_id']);
				$this->Mailer->Subject = "You've received a Make Music New York request!";
   				$this->Mailer->Message = 
   				"Someone has asked you to perform at their location for Make Music New York!\n\n".
  				"Location:  ".$req_location['Location']['locationname'].
   				"\nType of Location:  ".$res_location['Location']['type'].
   				"\nAddress:  ".$res_location['Location']['street_add1'].", ".$res_location['Location']['city']." ".$res_location['Location']['state'].
   				"\nTime Requested:  ".$this->data['Performance']['start_time']." - ".$this->data['Performance']['end_time'].
   				"\nNotes from Location:  ".$this->data['Performance']['location_notes'].
   				
   				"\n\nPlease login to approve, decline, or modify this request:\nhttp://www.".APP_DOMAIN_LOCATION."".
				$this->emailClose;
   				$this->Mailer->AddAddress($fullname , $email);
    			$this->Mailer->socketmail();
				$this->Session->setFlash('<div class="success">Thank you!<br>An e-mail has been sent to '.$book_artist['Artist']['groupname'].' informing them of your request.<br>You will be notified once they approve or deny it.<br>Please note that final approval from MMNY is required for all performances.</div>'); 
	  			$this->redirect('/users/pending');				
			}
 		}
 	}

 	function search()
	{
		$this->checkSession();
		
		$search_term = $this->data['Artist']['search'];
//		$this->set('data',NULL);
		$this->set('searchterm', $search_term);
       	$conditions =array();
        $criteria = array("or" => array('Artist.groupname' => 'LIKE %'.$search_term.'%', 'User.zip_code' => 'LIKE %'.$search_term.'%'));
		$this->Pagination->show = 20;
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Artist->findAll($criteria, '', 'Artist.groupname ASC', $limit, $page);
		$this->set('data',$data);
  	}  
	function locations($id = null) {
		$criteria=$id;
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC', $limit, $page);
		$this->set('data',$data);				 
		foreach ($data as $output)  {
			foreach ($output['Location'] as $k=>$v)  {  
			   // echo ( $k."=".$v."<br />" ); 
				if ($k == "id") {
			    	$id = $v;  
				}
				if ($k == "locationname") {
			    	$locationname = $v;  
				}
			}
	    	$locationArray[$id] = $locationname;  
		}   
		return $locationArray;
	}
}  
                         

?>