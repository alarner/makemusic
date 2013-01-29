<?php
class PerformancesController extends AppController
{

	var $name = 'Performances';
	var $uses = array('Performance','Location','Artist', 'User');
	var $components = array('SimpleGet', 'Mailer', 'Pagination');
	var $helpers = array('Pagination', 'Javascript');
	var $layout = 'performances';
	var $emailClose = "\n\nThank You,\nMake Music New York 2012";
	
	function confirm($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		                     	
		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findByUser_id($now_user['User']['id']);
		$this->set('Artist', $now_artist);
		$now_location = $this->Location->findByUser_id($now_user['User']['id']);
		$this->set('Location', $now_location);

		$criteria=$now_user['User']['id'];
		$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
		$locationArray = array();   
		foreach ($data as $output)  {
			foreach ($output['Location'] as $k=>$v)  {  
				//echo ( $k."=".$v."<br />" ); 
				if ($k == "id") {
			    	$locationArray[] = $v;  
				}
			}
		}   

		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div><br>'); 
  			$this->redirect('/users'); 
 		}
 		if ($now_artist && $now_user['User']['user_type'] == '160')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$this->set('otherlocation',$other_location);
			$other_user = $this->User->findById($other_location['Location']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['location_confirmed'] == '1')
					{
						if ($now_per['Performance']['artist_id'] == $now_artist['Artist']['id'])
						{
							$this->data['Performance']['artist_confirmed'] = '1';
							if ($this->Performance->save($this->data, false))
							{
								$this->Mailer->Subject = "Make Music NY Performance Request Approved";
								$this->Mailer->Message = 
 								"An Artist has approved your request for their performance at your location.".
   								"\nHere are the details:".
   								"\n\nThe Artist:  ".$now_artist['Artist']['groupname'].
   								"\nIs Playing: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
								"\nAt Location:  ".$other_location['Location']['locationname'].
								"\nNotes from Artist:  ".$this->data['Performance']['artist_notes'].
   								"\n\nMake Music NY organizers will now verify your performance details. You will be notified shortly when verification is complete.".
   								"\n\nUntil then, you will be able to see your concert details under 'Pending Performances' when you log in at: www.".APP_DOMAIN_LOCATION."".
   								$this->emailClose;
								$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   								$this->Mailer->socketmail();
								$this->Session->setFlash('<div class="success">Thank you! An e-mail has been sent to '.$other_location['Location']['locationname'].' informing them of your approval.<br>Please note that final approval from MMNY is required for all performances.</div><br>');
								$this->redirect('/users/pending'); 
							}
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
					}
 					else
					{
						$this->Session->setFlash('<div class="error">Location has not confirmed Performance yet!</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
 			}
 		}
 		if ($now_location && $now_user['User']['user_type'] == '180')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$other_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
			$this->set('otherartist',$other_artist);
			$other_user = $this->User->findById($other_artist['Artist']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['artist_confirmed'] == '1')
					{

						if (in_array($now_per['Performance']['location_id'], $locationArray))
						{
							$this->data['Performance']['location_confirmed'] = '1';
							if ($this->Performance->save($this->data, false))
							{
								$this->Mailer->Subject = "Make Music NY Performance Request Approved";
								$this->Mailer->Message = 
 								"A location has approved your performance request!".
   								"\n\nHere are the details:".
   							  	"\n\nYour Artist:  ".$other_artist['Artist']['groupname'].
   								"\nIs playing: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
								"\nAt Location:  ".$other_location['Location']['locationname'].
   								"\nNotes from Location:  ".$this->data['Performance']['location_notes'].
   								"\n\nMake Music NY organizers will now verify your performance details. You will be notified shortly when verification is complete.".
   								"\n\nUntil then, you will be able to see your concert details under 'Pending Performances' when you log in at: www.".APP_DOMAIN_LOCATION."".
   								$this->emailClose;
								$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   								$this->Mailer->socketmail();
								$this->Session->setFlash('<div class="success">Thank you! An e-mail has been sent to '.$other_artist['Artist']['groupname'].' informing them of your approval.<br>Please note that final approval from MMNY is required for all performances.</div><br>');
								$this->redirect('/users/pending');
							}
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
					}
 					else
					{
						$this->Session->setFlash('<div class="error">Artist has not confirmed Performance yet!</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
			}
 		}
	}
	function deny($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		
		$this->pageTitle = 'Deny';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findByUser_id($now_user['User']['id']);
		$this->set('Artist', $now_artist);
		$now_location = $this->Location->findByUser_id($now_user['User']['id']);
		$this->set('Location', $now_location);
 
		$criteria=$now_user['User']['id'];
		$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
		$locationArray = array();   
		foreach ($data as $output)  {
			foreach ($output['Location'] as $k=>$v)  {  
				//echo ( $k."=".$v."<br />" ); 
				if ($k == "id") {
			    	$locationArray[] = $v;  
				}
			}
		}   

   		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div><br>'); 
  			$this->redirect('/users'); 
 		}
	 		if ($now_artist && $now_user['User']['user_type'] == '160')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$this->set('otherlocation',$other_location);
			$other_user = $this->User->findById($other_location['Location']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['location_confirmed'] == '1')
					{
						if ($now_per['Performance']['artist_id'] == $now_artist['Artist']['id'])
						{
							if (!empty($this->data['Performance']['artist_notes']))
							{
								$reason = "\n\nReason for decline:  ".$this->data['Performance']['artist_notes'];
							}
							else
							{
								$reason = "";
							}								
								$this->Mailer->Subject = "Make Music NY Performance Request Declined by Artist";
								$this->Mailer->Message = 
 								"An Artist has declined your request for their performance at your location.".
   								"\nHere are the details:".
								$reason.
   							  	"\n\nYour Location:  ".$other_location['Location']['locationname'].
   								"\nRequested Artist:  ".$now_artist['Artist']['groupname'].
   								"\nTime Requested: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
								"\n\nTo find another possible artist -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
   							$this->emailClose;
								$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   								$this->Mailer->socketmail();
   								$this->Performance->del($id);
								$this->Session->setFlash('<div class="success">Thank you. We have notified '.$other_location['Location']['locationname'].' that you have declined their performance request.</div><br>');
								$this->redirect('/users');
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
					}
 					else
					{
						$this->Session->setFlash('<div class="error">Location has not confirmed Performance yet!</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
 			}
 		}
 		if ($now_location && $now_user['User']['user_type'] == '180')
 		{
			$other_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
			$this->set('otherartist',$other_artist);
			$other_user = $this->User->findById($other_artist['Artist']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['artist_confirmed'] == '1')
					{
						if (in_array($now_per['Performance']['location_id'], $locationArray))
						//if ($now_per['Performance']['location_id'] == $now_location['Location']['id'])
						{
							if (!empty($this->data['Performance']['location_notes']))
							{
								$reason = "\n\nReason for decline:  ".$this->data['Performance']['location_notes'];
							}
							else
							{
								$reason = "";
							}
								$this->Mailer->Subject = "Make Music NY Performance Request Declined by Location";
   								$req_location = $this->Location->findById($now_per['Performance']['location_id']);
								$this->Mailer->Message = 
 								"A location has declined your performance request.".
   								"\nHere are the details:".
								$reason.
   							  	"\n\nYour Artist:  ".$other_artist['Artist']['groupname'].
				  				"\nRequested Location:  ".$req_location['Location']['locationname'].
				 				
								//"\nRequested Location:  ".$now_location['Location']['locationname'].
   								"\nTime Requested: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
								"\n\nTo find another possible location -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
   								$this->emailClose;
								$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   								$this->Mailer->socketmail();
   								$this->Performance->del($id);
								$this->Session->setFlash('<div class="success">Thank you. We have notified '.$other_artist['Artist']['groupname'].' that you have declined their performance request.</div><br>');
								$this->redirect('/users');
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
					}
 					else
					{
						$this->Session->setFlash('<div class="error">Artist has not confirmed Performance yet!</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
			}
 		}
	}
	function edit($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		
		$this->pageTitle = 'Edit';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findByUser_id($now_user['User']['id']);
		$this->set('Artist', $now_artist);
		$now_location = $this->Location->findByUser_id($now_user['User']['id']);
		$this->set('Location', $now_location);
		$startcolon = strrpos($now_per['Performance']['start_time'],":");
		$endcolon = strrpos($now_per['Performance']['end_time'],":");
		$this->set('acl',false);
		$this->set('lca',false);   
		
		$criteria=$now_user['User']['id'];
		$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
		$locationArray = array();   
		foreach ($data as $output)  {
			foreach ($output['Location'] as $k=>$v)  {  
				//echo ( $k."=".$v."<br />" ); 
				if ($k == "id") {
			    	$locationArray[] = $v;  
				}
			}
		}   
		 
		if ($startcolon == '2')
		{
			$this->set('start1',substr($now_per['Performance']['start_time'],0,2));
			$this->set('start2',substr($now_per['Performance']['start_time'],3,2));
			$this->set('start3',substr($now_per['Performance']['start_time'],6,2));
		}
		if ($startcolon == '1')
		{
			$this->set('start1',substr($now_per['Performance']['start_time'],0,1));
			$this->set('start2',substr($now_per['Performance']['start_time'],2,2));
			$this->set('start3',substr($now_per['Performance']['start_time'],5,2));
		}
		if ($endcolon == '2')
		{
			$this->set('end1',substr($now_per['Performance']['end_time'],0,2));
			$this->set('end2',substr($now_per['Performance']['end_time'],3,2));
			$this->set('end3',substr($now_per['Performance']['end_time'],6,2));
		}
		if ($endcolon == '1')
		{
			$this->set('end1',substr($now_per['Performance']['end_time'],0,1));
			$this->set('end2',substr($now_per['Performance']['end_time'],2,2));
			$this->set('end3',substr($now_per['Performance']['end_time'],5,2));
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div>'); 
  		   	$this->redirect('/users/pending'); 
 		}
 		if ($now_per['Performance']['acl'] == '1')
 		{
	   	   // $this->redirect('/users/pending');
 		}
		
 		
 		if ($now_per['Performance']['lca'] == '1')
		{
	   	   // $this->redirect('/users/pending'); 				
		}
 		
 		
		if ($now_artist && $now_user['User']['user_type'] == '160')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$this->set('otherlocation',$other_location);
			$other_user = $this->User->findById($other_location['Location']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['artist_id'] == $now_artist['Artist']['id'])
					{
						$this->cleanUpFields();
						$now_per['Performance']['location_confirmed'] = '0';
						$now_per['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
						$now_per['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
						$now_per['Performance']['artist_notes'] = $this->data['Performance']['artist_notes']; 
						$now_per['Performance']['artist_confirmed'] = '1';
						if ($this->Performance->save($now_per['Performance'], false))
						{
							$this->Mailer->Subject = "Make Music NY Performance Request Approved With Changes";
							$this->Mailer->Message = 
 							"An artist has accepted your performance request, but has proposed some changes.".
   							"\nHere are the new details; please read them carefully.".
   						  	"\n\nYour Location:  ".$other_location['Location']['locationname'].
   							"\nTheir Artist:  ".$now_artist['Artist']['groupname'].
   							"\nTime Requested: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
							"\nNotes from Artist:  ".$this->data['Performance']['artist_notes'].
   							"\n\nNow that the request has been changed, you must approve, decline, or modify the new details under 'Pending Performances' when you log in to: www.".APP_DOMAIN_LOCATION."".
   							$this->emailClose;
							$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   							$this->Mailer->socketmail();
							$this->Session->setFlash('<div class="success">You have modified a performance request with '.$other_location['Location']['locationname'].'.<br>Now that the request has been changed, the Location now has to confirm, deny, or modify the request.</div><br>');
							$this->redirect('/users/pending');
							}
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
				}
 			}
 		}
 		
 		
 		if ($now_location && $now_user['User']['user_type'] == '180')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$other_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
			$this->set('otherartist',$other_artist);
			$other_user = $this->User->findById($other_artist['Artist']['user_id']);
			$this->set('otheruser',$other_user);
 			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if (in_array($now_per['Performance']['location_id'], $locationArray))
					{
						$this->cleanUpFields();
						$now_per['Performance']['artist_confirmed'] = '0';
						$now_per['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
						$now_per['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
						$now_per['Performance']['location_notes'] = $this->data['Performance']['location_notes']; 
						$now_per['Performance']['location_confirmed'] = '1';
						if ($this->Performance->save($now_per['Performance'], false))
						{
							$this->Mailer->Subject = "Make Music NY Performance Request Approved With Changes";
							$this->Mailer->Message = 
 							"A location has accepted your performance request, but has proposed some changes.".
   							"\nHere are the new details; please read them carefully.".
   						  	"\n\nYour Artist:  ".$other_artist['Artist']['groupname'].
							"\nTheir Location:  ".$other_location['Location']['locationname'].
   							"\nTime Requested: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
							"\nNotes from Location:  ".$this->data['Performance']['location_notes'].
   							"\n\nNow that the request has been changed, you must approve, decline, or modify the new details under 'Pending Performances' when you log in to: www.".APP_DOMAIN_LOCATION."".
   							$this->emailClose;
							$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   							$this->Mailer->socketmail();
							$this->Session->setFlash('<div class="success">You have modified a performance request with '.$other_artist['Artist']['groupname'].'.<br>Now that the request has been changed, the Artist now has to confirm, deny, or modify the request.</div><br>');
							$this->redirect('/users/pending');
						}
					}
					else
					{
	   					$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
 			}
 		}
	}
	function view($id = null)
	{
		$this->checkSession();
		
		$this->pageTitle = 'View';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] == '220')
		{
			$this->redirect('/admin/performances/view/'.$id); 
		}
		
		$this->set('artist_booked',false);
		$this->set('artist_requested',false);		

		$this->set('location_booked',false);
		$this->set('location_requested',false);				
		
		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		
		$now_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
		$this->set('Artist', $now_artist['Artist']);
		
		$artistuser = $this->User->findById($now_artist['Artist']['user_id']);
		$this->set('ArtistUser',$artistuser);
		
		$res_artist = $this->Artist->findByUser_id($now_user['User']['id']);	
		
		$now_location = $this->Location->findById($now_per['Performance']['location_id']);
		$this->set('Location', $now_location['Location']);
		
		$locationuser = $this->User->findById($now_location['Location']['user_id']);
		$this->set('LocationUser',$locationuser);
		
		$res_location = $this->Location->findByUser_id($now_user['User']['id']);

		$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $now_artist['Artist']['id'])));		

		$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.location_id' => $now_location['Location']['id'])));		
		
		$artist_booked = $this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $now_artist['Artist']['id'], 'Performance.location_id' => $res_location['Location']['id']));
		$artist_pending = $this->Performance->findAll(array("and" => array('Performance.artist_id' => $now_artist['Artist']['id'], 'Performance.location_id' => $res_location['Location']['id']), "or" => array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')));
		if ($artist_booked)
		{
			$this->set('artist_booked',true);
		}
		if ($artist_pending)
		{
			$this->set('artist_requested',true);
		}
		
		$location_booked = $this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.location_id' => $now_location['Location']['id']));
		$location_pending = $this->Performance->findAll(array("and" => array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.location_id' => $now_location['Location']['id']), "or" => array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')));
		if ($location_booked)
		{
			$this->set('location_booked',true);
		}
		if ($location_pending)
		{
			$this->set('location_requested',true);
		}		
	}
	function remove($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		
		$this->pageTitle = 'Remove';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findByUser_id($now_user['User']['id']);
		$this->set('Artist', $now_artist);
		$now_location = $this->Location->findByUser_id($now_user['User']['id']);
		$this->set('Location', $now_location);     
		
		$criteria=$now_user['User']['id'];
		$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
		$locationArray = array();   
		foreach ($data as $output)  {
			foreach ($output['Location'] as $k=>$v)  {  
				//echo ( $k."=".$v."<br />" ); 
				if ($k == "id") {
			    	$locationArray[] = $v;  
				}
			}
		}   
		
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div>'); 
  			$this->redirect('/users/pending'); 
 		}
	 		if ($now_artist && $now_user['User']['user_type'] == '160')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$this->set('otherlocation',$other_location);
			$other_user = $this->User->findById($other_location['Location']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['location_confirmed'] == '1')
					{
						if ($now_per['Performance']['artist_id'] == $now_artist['Artist']['id'])
						{
							if (!empty($this->data['Performance']['artist_notes']))
							{
								$reason = "\n\nReason for removal:  ".$this->data['Performance']['artist_notes'];
							}
							else
							{
								$reason = "";
							}								
								$this->Mailer->Subject = "Make Music NY Pending Performance Canceled by Artist";
								$this->Mailer->Message = 
 								"An Artist has canceled a pending performance with your location.".
   								"\nHere are the details:".
								$reason.
   							  	"\n\nYour Location:  ".$other_location['Location']['locationname'].
   								"\nTheir Artist:  ".$now_artist['Artist']['groupname'].
   								"\nTime Requested: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
								"\n\nTo find another possible artist -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
   							$this->emailClose;
								$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   								$this->Mailer->socketmail();
   								$this->Performance->del($id);
								if ($now_per['Performance']['acl'] == '1')
								{
									$this->Location->del($now_location['Location']['id']);
									$this->Session->setFlash('<div class="success">This Performance has been Canceled and the Location has been Removed.</div><br>'); 
									$this->redirect('/users'); 	
								}
								if ($now_per['Performance']['lca'] == '1')
								{
									$this->Artist->del($now_artist['Artist']['id']);
									$this->Session->setFlash('<div class="success">This Performance has been Canceled and Artist has been Removed.</div><br>'); 
									$this->redirect('/users'); 	
								}
								if (($now_per['Performance']['lca'] == '1') && ($now_per['Performance']['lca'] == '1'))
								{
									$this->Session->setFlash('<div class="success">You have canceled the pending performance involving '.$other_location['Location']['locationname'].'.<br>An e-mail informing them of your cancelation has been sent.</div><br>');
									$this->redirect('/users');
								}
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
					}
 					else
					{
						$this->Session->setFlash('<div class="error">Your performance has been canceled.</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
 			}
 		}
 		if ($now_location && $now_user['User']['user_type'] == '180')
 		{
 			$other_location = $this->Location->findById($now_per['Performance']['location_id']);
			$other_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
			$this->set('otherartist',$other_artist);
			$other_user = $this->User->findById($other_artist['Artist']['user_id']);
			$this->set('otheruser',$other_user);
			if (!empty($this->data))
 			{
 				if ($this->User->validates($this->data))
 				{
					if ($now_per['Performance']['artist_confirmed'] == '1')
					{
						if (in_array($now_per['Performance']['location_id'], $locationArray))
						//if ($now_per['Performance']['location_id'] == $now_location['Location']['id'])
						{
							if (!empty($this->data['Performance']['location_notes']))
							{
								$reason = "\n\nReason for removal:  ".$this->data['Performance']['location_notes'];
							}
							else
							{
								$reason = "";
							}
								$this->Mailer->Subject = "Make Music NY Pending Performance Canceled by Location";
								$this->Mailer->Message = 
 								"A location has canceled a pending performance with your artist.".
   								"\nHere are the details:".
								$reason.
   							  	"\n\nYour Artist:  ".$other_artist['Artist']['groupname'].
   								"\nTheir Location:  ".$other_location['Location']['locationname'].
   								"\nTime Requested: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
								"\n\nTo find another possible location -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
   							$this->emailClose;
								$this->Mailer->AddAddress($other_user['User']['first_name']." ".$other_user['User']['last_name'] , $other_user['User']['emailaddress']);
   								$this->Mailer->socketmail();
   								$this->Performance->del($id);
								if ($now_per['Performance']['acl'] == '1')
								{
									$this->Location->del($now_location['Location']['id']);
									$this->Session->setFlash('<div class="success">This Performance has been Canceled and the Location has been Removed.</div><br>'); 
									$this->redirect('/users'); 	
								}
								if ($now_per['Performance']['lca'] == '1')
								{
									$this->Artist->del($now_artist['Artist']['id']);
									$this->Session->setFlash('<div class="success">This Performance has been Canceled and Artist has been Removed.</div><br>'); 
									$this->redirect('/users'); 	
								}
								if (($now_per['Performance']['lca'] == '1') && ($now_per['Performance']['lca'] == '1'))
								{
									$this->Session->setFlash('<div class="success">You have canceled the pending performance involving '.$other_artist['Artist']['groupname'].'.<br>An e-mail informing them of your cancelation has been sent.</div><br>');
									$this->redirect('/users');
								}
						}
						else
						{
	   						$this->Session->setFlash('<div class="error">This is not your Performance</div><br>'); 
  							$this->redirect('/users'); 				
						}
					}
 					else
					{
						$this->Session->setFlash('<div class="error">Your performance has been canceled.</div><br>'); 
  						$this->redirect('/users'); 				
					}
				}
			}
 		}
	}
	function index()
	{
		$this->checkSession();
		
		$this->pageTitle = 'Confirmed Performances';
		$criteria=array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1');
		//$order = 'groupname ASC';
		$this->Pagination->resultsPerPage = array(10); 
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated DESC', $limit, $page);
		$this->set('data',$data); 
	}

	function search()
	{
		$this->checkSession();
		
		$search_term = $this->data['Performance']['search'];
//		$this->set('data',NULL);
		$this->set('searchterm', $search_term);		
       	$conditions =array();
        $criteria = array("and" => array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1'), "or" => array('Artist.groupname' => 'LIKE %'.$search_term.'%', 'Location.locationname' => 'LIKE %'.$search_term.'%', 'Location.street_add1' => 'LIKE %'.$search_term.'%', 'Location.street_add2' => 'LIKE %'.$search_term.'%', 'Location.city' => 'LIKE %'.$search_term.'%', 'Location.zip_code' => 'LIKE %'.$search_term.'%', 'Location.hood' => 'LIKE %'.$search_term.'%', 'Location.type' => 'LIKE %'.$search_term.'%'));
		$this->Pagination->resultsPerPage = array(10);  
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated DESC', $limit, $page);
		$this->set('data',$data);
	}	
	function admin_confirm($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
		$this->set('Artist', $now_artist);
		$artist_user = $this->User->findById($now_artist['Artist']['user_id']);
		$this->set('ArtistUser',$artist_user);
		$now_location = $this->Location->findById($now_per['Performance']['location_id']);
		$this->set('Location', $now_location);
		$location_user = $this->User->findById($now_location['Location']['user_id']);
		$this->set('LocationUser',$location_user);		
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div><br>'); 
  			$this->redirect('/admin/users'); 
 		}
		if (!empty($this->data))
		{
			if ($this->User->validates($this->data))
			{
				if ($now_per['Performance']['location_confirmed'] == '1' && $now_per['Performance']['artist_confirmed'] == '1')
				{
					$this->data['Performance']['admin_confirmed'] = '1';
					if ($this->Performance->save($this->data, false))
					{
						if ($now_per['Performance']['acl'] == '0' && $now_per['Performance']['lca'] == '0')
						{
							$this->Mailer->Subject = "Make Music NY has Approved Your Performance Request";
							$this->Mailer->Message = 
 							"Congratulations! Make Music New York has approved your request for a concert on Thursday, June 21st.".
   							"\nHere are the details:".
   							"\n\nArtist:  ".$now_artist['Artist']['groupname'].
							"\nLocation:  ".$now_location['Location']['locationname'].
							"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
							"\nConcert Description:  ".$this->data['Performance']['description'].
	   						"\n\nYou can now view this under the 'Confirmed Performances' tab in your account at:  www.".APP_DOMAIN_LOCATION."". 
							"\n\nMake Music New York will now apply to the city for your concert permits, based on the information you’ve provided. You will receive all permits shortly before June 21st. We will be in touch if any additional questions come up.".	
   							$this->emailClose;
							$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
							$this->Mailer->AddAddress($location_user['User']['first_name']." ".$location_user['User']['last_name'] , $location_user['User']['emailaddress']);
							$this->Mailer->socketmail();
   							$this->Session->setFlash('<div class="success">This Performance is now Confirmed!.</div><br>'); 
							$this->redirect('/admin/users/confirmed'); 				
						}
						else
						{
							$this->Mailer->Subject = "Make Music NY has Approved Your Performance Request";
							$this->Mailer->Message = 
	 						"Congratulations! Make Music New York has approved your request for a concert on Thursday, June 21st.".
   							"\nHere are the details:".
   							"\n\nArtist:  ".$now_artist['Artist']['groupname'].
							"\nLocation:  ".$now_location['Location']['locationname'].
							"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
							"\nConcert Description:  ".$this->data['Performance']['description'].
	   						"\n\nYou can now view this under the 'Confirmed Performances' tab in your account at:  www.".APP_DOMAIN_LOCATION."".
							"\n\nMake Music New York will now apply to the city for your concert permits, based on the information you’ve provided. You will receive all permits shortly before June 21st. We will be in touch if any additional questions come up.".
   							$this->emailClose;
							$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
							$this->Mailer->socketmail();
   							$this->Session->setFlash('<div class="success">This Performance is now Confirmed!.</div><br>'); 
							$this->redirect('/admin/users/confirmed'); 				
						}
					}
				}
				else
				{
   					$this->Session->setFlash('<div class="error">This Performance is not waiting your approval.</div><br>'); 
					$this->redirect('/admin/users/pending'); 				
				}
			}
		}
	}
	function admin_deny($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		$this->pageTitle = 'Deny';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
		$this->set('Artist', $now_artist);
		$artist_user = $this->User->findById($now_artist['Artist']['user_id']);
		$this->set('ArtistUser',$artist_user);
		$now_location = $this->Location->findById($now_per['Performance']['location_id']);
		$this->set('Location', $now_location);
		$location_user = $this->User->findById($now_location['Location']['user_id']);
		$this->set('LocationUser',$location_user);		
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div><br>'); 
  			$this->redirect('/admin/users'); 
 		}
		if (!empty($this->data))
		{
			if ($this->User->validates($this->data))
			{
				if ($now_per['Performance']['location_confirmed'] == '1' && $now_per['Performance']['artist_confirmed'] == '1')
				{
					if (!empty($this->data['Performance']['description']))
					{
						$reason = "\n\nReason for denial:  ".$this->data['Performance']['description'];
					}
					else
					{
						$reason = "";
					}
					// ACL = ARTIST CREATED  // LOCATION LCA = LOCATION CREATED ARTIST								
					if ($now_per['Performance']['acl'] == '0' && $now_per['Performance']['lca'] == '0')
					{
						$this->Mailer->Subject = "Make Music NY Performance Request Denied by MMNY";
						$this->Mailer->Message = 
 						"Make Music NY has denied your performance.".
   						"\nHere are the details:".
						$reason.
   						"\n\nArtist:  ".$now_artist['Artist']['groupname'].
						"\nLocation:  ".$now_location['Location']['locationname'].
						"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
						"\n\nTo find another possible performance -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
						$this->emailClose;
						$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
						$this->Mailer->AddAddress($location_user['User']['first_name']." ".$location_user['User']['last_name'] , $location_user['User']['emailaddress']);
						$this->Mailer->socketmail();
   						$this->Performance->del($id);
						$this->Session->setFlash('<div class="success">This Performance is now Denied!.</div><br>'); 
						$this->redirect('/admin/users'); 				
					}
					else
					{
						$this->Mailer->Subject = "Make Music NY Performance Request Denied by MMNY";
						$this->Mailer->Message = 
 						"Make Music NY has denied your performance.".
   						"\nHere are the details:".
						$reason.
   						"\n\nArtist:  ".$now_artist['Artist']['groupname'].
						"\nLocation:  ".$now_location['Location']['locationname'].
						"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
						"\n\nTo find another possible performance -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
						$this->emailClose;
						$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
						$this->Mailer->socketmail();
   						$this->Performance->del($id);
						if ($now_per['Performance']['acl'] == '1')
						{
							$this->Location->del($now_location['Location']['id']);
							$this->Session->setFlash('<div class="success">This Performance is now Denied! And the Artist Created Location has been Removed.</div><br>'); 
							$this->redirect('/admin/users'); 	
						}
						if ($now_per['Performance']['lca'] == '1')
						{
							$this->Artist->del($now_artist['Artist']['id']);
							$this->Session->setFlash('<div class="success">This Performance is now Denied! And the Location Created Artist has been Removed.</div><br>'); 
							$this->redirect('/admin/users'); 	
						}
					}
				}
 				else
				{
					$this->Session->setFlash('<div class="error">This Performance is not waiting your approval.</div><br>'); 
  					$this->redirect('/users'); 				
				}
			}
 		}
 	}
	function admin_edit($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		$this->pageTitle = 'Edit';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
		$this->set('Artist', $now_artist);
		$artist_user = $this->User->findById($now_artist['Artist']['user_id']);
		$this->set('ArtistUser',$artist_user);
		$now_location = $this->Location->findById($now_per['Performance']['location_id']);
		$this->set('Location', $now_location);
		$location_user = $this->User->findById($now_location['Location']['user_id']);
		$this->set('LocationUser',$location_user);		
		$startcolon = strrpos($now_per['Performance']['start_time'],":");
		$endcolon = strrpos($now_per['Performance']['end_time'],":");
		$this->set('acl',false);
		$this->set('lca',false);	
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		if ($startcolon == '2')
		{
			$this->set('start1',substr($now_per['Performance']['start_time'],0,2));
			$this->set('start2',substr($now_per['Performance']['start_time'],3,2));
			$this->set('start3',substr($now_per['Performance']['start_time'],6,2));
		}
		if ($startcolon == '1')
		{
			$this->set('start1',substr($now_per['Performance']['start_time'],0,1));
			$this->set('start2',substr($now_per['Performance']['start_time'],2,2));
			$this->set('start3',substr($now_per['Performance']['start_time'],5,2));
		}
		if ($endcolon == '2')
		{
			$this->set('end1',substr($now_per['Performance']['end_time'],0,2));
			$this->set('end2',substr($now_per['Performance']['end_time'],3,2));
			$this->set('end3',substr($now_per['Performance']['end_time'],6,2));
		}
		if ($endcolon == '1')
		{
			$this->set('end1',substr($now_per['Performance']['end_time'],0,1));
			$this->set('end2',substr($now_per['Performance']['end_time'],2,2));
			$this->set('end3',substr($now_per['Performance']['end_time'],5,2));
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div><br>'); 
  			$this->redirect('/admin/users'); 
 		}
		if (!empty($this->data))
		{
			if ($this->User->validates($this->data))
			{
				$this->cleanUpFields();
				if ($now_per['Performance']['acl'] == '1')
				{
					$this->set('aclper',true);
					$now_per['Performance']['artist_confirmed'] = $this->data['Performance']['artist_confirmed'];
					$now_per['Performance']['location_confirmed'] = "1";
				}
				if ($now_per['Performance']['lca'] == '1')
				{
					$this->set('lcaper',true);
					$now_per['Performance']['location_confirmed'] = $this->data['Performance']['location_confirmed'];
					$now_per['Performance']['artist_confirmed'] = "1";
				}
				if ($now_per['Performance']['lca'] == '0' && $now_per['Performance']['acl'] == '0' && $now_location['Location']['type'] != 'Park')
				{
					$now_per['Performance']['artist_confirmed'] = $this->data['Performance']['artist_confirmed'];
					$now_per['Performance']['location_confirmed'] = $this->data['Performance']['location_confirmed'];		
				}
				$now_per['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
				$now_per['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
				$now_per['Performance']['description'] = $this->data['Performance']['description'];
				if ($this->Performance->save($now_per['Performance'], false))
				{
					if ($now_per['Performance']['acl'] == '0' && $now_per['Performance']['lca'] == '0')
					{
						$this->Mailer->Subject = "Make Music NY Performance Request Edited By MMNY";
						$this->Mailer->Message = 
						"MMNY has edited your performance request and proposed some changes.".
   						"\nHere are the new details; please read them carefully.".
   						"\n\nArtist:  ".$now_artist['Artist']['groupname'].
						"\nLocation:  ".$now_location['Location']['locationname'].
						"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
						"\nNotes from MMNY:  ".$this->data['Performance']['description'].
   						"\n\nNow that the request has been changed, you can see the new details under 'Pending Performances' when you log in to: www.".APP_DOMAIN_LOCATION."".
						$this->emailClose;
						$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
						$this->Mailer->AddAddress($location_user['User']['first_name']." ".$location_user['User']['last_name'] , $location_user['User']['emailaddress']);
						$this->Mailer->socketmail();
   						$this->Session->setFlash('<div class="success">This Performance is now Modified!.</div><br>'); 
						$this->redirect('/admin/users');
					}
					else
					{
						$this->Mailer->Subject = "Make Music NY Performance Request Edited By MMNY";
						$this->Mailer->Message = 
						"MMNY has edited your performance request and proposed some changes.".
   						"\nHere are the new details; please read them carefully.".
   						"\n\nArtist:  ".$now_artist['Artist']['groupname'].
						"\nLocation:  ".$now_location['Location']['locationname'].
						"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
						"\nNotes from MMNY:  ".$this->data['Performance']['description'].
   						"\n\nNow that the request has been changed, you can see the new details under 'Pending Performances' when you log in to: www.".APP_DOMAIN_LOCATION."".
						$this->emailClose;
						$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
						$this->Mailer->socketmail();
   						$this->Session->setFlash('<div class="success">This Performance is now Modified!.</div><br>'); 
						$this->redirect('/admin/users'); 				
					}
				}
			}
		}
	}
	function admin_view($id = null)
	{
		$this->checkSession();
		$this->pageTitle = 'View';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
		$this->set('Artist', $now_artist['Artist']);
		$this->set('ArtistUser',$this->User->findById($now_artist['Artist']['user_id']));
		$now_location = $this->Location->findById($now_per['Performance']['location_id']);
		$this->set('Location', $now_location['Location']);
		$this->set('LocationUser',$this->User->findById($now_location['Location']['user_id']));
		$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $now_artist['Artist']['id'])));		
		$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.location_id' => $now_location['Location']['id'])));		
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}		
	}
	function admin_remove($id = null)
	{
		$this->layout = 'myaccount';
		$this->checkSession();
		$this->pageTitle = 'Remove';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
  		$now_per = $this->Performance->read(null, $id);
		$this->set('Performance', $now_per);
		$now_artist = $this->Artist->findById($now_per['Performance']['artist_id']);
		$this->set('Artist', $now_artist);
		$artist_user = $this->User->findById($now_artist['Artist']['user_id']);
		$this->set('ArtistUser',$artist_user);
		$now_location = $this->Location->findById($now_per['Performance']['location_id']);
		$this->set('Location', $now_location);
		$location_user = $this->User->findById($now_location['Location']['user_id']);
		$this->set('LocationUser',$location_user);		
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown Performance</div><br>'); 
  			$this->redirect('/admin/users'); 
 		}
		if (!empty($this->data))
		{
			if ($this->User->validates($this->data))
			{
				if ($now_per['Performance']['location_confirmed'] == '1' || $now_per['Performance']['artist_confirmed'] == '1')
				{
					if (!empty($this->data['Performance']['description']))
					{
						$reason = "\n\nReason for removal:  ".$this->data['Performance']['description'];
					}
					else
					{
						$reason = "";
					}								
					if ($now_per['Performance']['acl'] == '0' && $now_per['Performance']['lca'] == '0')
					{
						$this->Mailer->Subject = "Make Music NY Performance Removed by MMNY";
						$this->Mailer->Message = 
 						"Make Music NY has removed your performance.".
   						"\n\nHere are the details:".
						$reason.
   						"\n\nArtist:  ".$now_artist['Artist']['groupname'].
						"\nLocation:  ".$now_location['Location']['locationname'].
						"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
						"\n\nTo find another possible performance -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
						$this->emailClose;
						$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
						$this->Mailer->AddAddress($location_user['User']['first_name']." ".$location_user['User']['last_name'] , $location_user['User']['emailaddress']);
						$this->Mailer->socketmail();
   						$this->Performance->del($id);
						$this->Session->setFlash('<div class="success">This Performance is now Denied!.</div><br>'); 
						$this->redirect('/admin/users'); 				
					}
					else
					{
						$this->Mailer->Subject = "Make Music NY Performance Removed by MMNY";
						$this->Mailer->Message = 
 						"Make Music NY has removed your performance.".
   						"\n\nHere are the details:".
						$reason.
   						"\n\nArtist:  ".$now_artist['Artist']['groupname'].
						"\nLocation:  ".$now_location['Location']['locationname'].
						"\nTime: ".$now_per['Performance']['start_time']." - ".$now_per['Performance']['end_time'].
						"\n\nTo find another possible performance -- or to register your own -- please log in to: www.".APP_DOMAIN_LOCATION."".
						$this->emailClose;
						$this->Mailer->AddAddress($artist_user['User']['first_name']." ".$artist_user['User']['last_name'] , $artist_user['User']['emailaddress']);
						$this->Mailer->socketmail();
   						$this->Performance->del($id);
						if ($now_per['Performance']['acl'] == '1')
						{
							$this->Location->del($now_location['Location']['id']);
							$this->Session->setFlash('<div class="success">This Performance is now Denied! And the Artist Created Location has been Removed.</div><br>'); 
							$this->redirect('/admin/users/pending'); 	
						}
						if ($now_per['Performance']['lca'] == '1')
						{
							$this->Artist->del($now_artist['Artist']['id']);
							$this->Session->setFlash('<div class="success">This Performance is now Denied! And the Location Created Artist has been Removed.</div><br>'); 
							$this->redirect('/admin/users'); 	
						}
					}
				}
 				else
				{
					$this->Session->setFlash('<div class="error">This Performance is not waiting your approval.</div><br>'); 
  					$this->redirect('/users'); 				
				}
			}
 		}
 	}
}
?>