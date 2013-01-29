<?php 
class UsersController extends AppController 
{
	var $uses = array('User', 'Artist', 'Location','Performance');
	var $components = array('Mailer','SimpleGet', 'Pagination');
	var $helpers = array('Pagination','Csv', 'Javascript');
	var $layout = 'myaccount';  
	var $year = "2012";
	var $emailClose = "\n\nThank You,\nMake Music New York 2012";     
	var $locationArray = array();
	function resend()
		{ 
		$this->layout = 'participate';
		$this->pageTitle = 'Resend Activation';
		$this->set('emailaddress_error', 'Invalid e-mail!');
		if (!empty($this->data))
			{ 
			if ($this->User->validates($this->data))
				{
				if ($this->User->findByEmailaddress($this->data['User']['emailaddress']))
						{
						$res_user = $this->User->findByEmailaddress($this->data['User']['emailaddress']); 
						$email = $res_user['User']['emailaddress'];
		  				$fname = $res_user['User']['first_name'];
		  				$lname = $res_user['User']['last_name'];
		  				$fullname = $fname.' '.$lname;
		  				$zip_code = $res_user['User']['zip_code'];
		  				$hash = $res_user['User']['hash'];
		  				$url = 'http://'.APP_DOMAIN_LOCATION.'/users/activate/confirm/'.$hash;
		  				//if ($res_user['User']['user_type'] == '140')
		  				//$usertype = 'Newsletter Subscription';
		  				if ($res_user['User']['user_type'] == '160')
		  					{
		  					$usertype = 'Artist Registration';
		  					}
						if ($res_user['User']['user_type'] == '180')
		  					{
		  					$usertype = 'Location Registration';
		  					}
						if ($res_user['User']['status'] == '1')
							{
							$this->User->invalidate('emailaddress');
							$this->set('emailaddress_error', 'You are already activated!');
							}
						if ($res_user['User']['user_type'] == '140')
							{ 
							$this->User->invalidate('emailaddress');
							$this->set('emailaddress_error', 'Email Not Valid');
							}
							else
							{
							$this->Mailer->Subject = "Make Music NY $usertype";
   							$this->Mailer->Message = "Thank you for registering for Make Music New York ".$this->year." \n\nPlease click here to complete the sign-up process:\n".$url."\n\nFor your reference, here is the email address you signed up with:\n\n Email address: ".$res_user['User']['emailaddress'].$this->emailClose;
   							$this->Mailer->AddAddress($fullname , $email);
   							$this->Mailer->socketmail();
							//$this->flash('An Email has been sent to '.$email.' to confirm MMNY '.$usertype, '/users');
							$this->Session->setFlash('<div class="success">An e-mail has been sent to '.$email.' to confirm MMNY '.$usertype.'<br />');
							//' or <a href="'.$url.'">click here</a> to activate.</div><br>');
							$this->redirect('');
							}
						}
						else
						{
						$this->User->invalidate('emailaddress');
						$this->set('emailaddress_error', 'E-mail Does Not Exist.<br><a href="/makemusicny/participate">Signup for MMNY</a>');
						$this->validateErrors($this->User);
						}
					}
				}
			}	 
	function subscribe() 
		{
		$this->layout = 'participate';
		$this->pageTitle = 'Newsletter Subscription';
		$this->set('emailaddress_error', 'Invalid Email');
		$this->set('zip_code_error', 'Invalid Zip Code');
		if (!empty($this->data)) 
			{ 
			if ($this->User->validates($this->data))		
				{ 
				if ($this->data['User']['emailaddress'] != $this->data['User']['emailaddress2'])
					{
					$this->User->invalidate('emailaddress');
					$this->set('emailaddress_error', 'E-mail Addresses do not match');
					}
					else
					{
					if ($this->User->findByEmailaddress($this->data['User']['emailaddress']))
						{
						$res_user = $this->User->findByEmailaddress($this->data['User']['emailaddress']);
						if ($res_user['User']['newsletter_status'] == '1')
							{
							$this->User->invalidate('emailaddress');
							$this->set('emailaddress_error', 'This address is already subscribing to the MMNY Newsletter.');	
							}
							else
							{
							$res_user['User']['salutation'] =  $this->data['User']['salutation'];
							$res_user['User']['first_name'] =  $this->data['User']['first_name'];
							$res_user['User']['last_name'] =  $this->data['User']['last_name'];
							$res_user['User']['zip_code'] =  $this->data['User']['zip_code'];
							$res_user['User']['newsletter_status'] =  $this->data['User']['newsletter_status'];
							$this->set('User', $res_user['User']);
							if ($this->User->save($res_user['User'], false))
								{
								$this->Session->setFlash('<div class="success">Thank you! You are now subscribed to the MMNY Newsletter.</div><br>');
								$this->redirect('');
								}
									
							}
						}
						else
						{
						$this->User->save($this->data);
						$this->Session->setFlash('<div class="success">Thank you! You are now subscribed to the MMNY Newsletter.</div><br>');
						$this->redirect('');
						}
					}
				}
			}
			else
			{
			$this->validateErrors($this->User);
			}
		}
	function register()
	{
		$this->layout = 'participate';
		$this->pageTitle = 'Register';
		$this->set('emailaddress_error', 'Invalid Email');
		$this->set('password_error', 'Invalid Password');
		$this->set('zip_code_error', 'Invalid Zip Code');
		$this->set('agree_error', 'Agreement Error');
		
		if (!empty($this->data))
		{
			if ($this->User->validates($this->data))
			{
				$rand = $this->data['User']['rand'];
				$email = $this->data['User']['emailaddress'];
				$fname = $this->data['User']['first_name'];
				$lname = $this->data['User']['last_name'];
				$this->data['User']['ph_primary'] = $this->data['User']['phpri1'].$this->data['User']['phpri2'].$this->data['User']['phpri3'];
				$this->data['User']['ph_mobile'] = $this->data['User']['phmob1'].$this->data['User']['phmob2'].$this->data['User']['phmob3'];
				$fullname = $fname.' '.$lname;
				$zip_code = $this->data['User']['zip_code'];
				$hash = md5($email . $zip_code . $rand);
			  	$this->data['User']['hash'] = $hash;
			  	$url = 'http://'.APP_DOMAIN_LOCATION.'/users/activate/confirm/'.$hash;
			  	if ($this->data['User']['user_type'] == '160')
				{
					$usertype = 'Artist Registration';
				}else{
					$usertype = 'Location Registration';
				}
				if ($this->data['User']['emailaddress'] == $this->data['User']['emailaddress2'])
				{
					if ($this->data['User']['password'] == $this->data['User']['password2'])
					{						
					if 	($this->data['User']['agree'])
					{
						if ($this->User->findByEmailaddress($this->data['User']['emailaddress']))
						{
							$res_user = $this->User->findByEmailaddress($this->data['User']['emailaddress']);
							if ($res_user['User']['user_type'] == '140')
							{
			  					$res_user['User']['salutation'] =  $this->data['User']['salutation'];
								$res_user['User']['user_type'] =  $this->data['User']['user_type'];
								$res_user['User']['password'] =  md5($this->data['User']['password']);
								$res_user['User']['street_add1'] =  $this->data['User']['street_add1'];
								$res_user['User']['street_add2'] =  $this->data['User']['street_add2'];
								$res_user['User']['city'] =  $this->data['User']['city'];
								$res_user['User']['first_name'] =  $this->data['User']['first_name'];
								$res_user['User']['last_name'] =  $this->data['User']['last_name'];
								$res_user['User']['zip_code'] =  $this->data['User']['zip_code'];
								$res_user['User']['state'] =  $this->data['User']['state'];
								$res_user['User']['ph_primary'] =  $this->data['User']['phpri1'].$this->data['User']['phpri2'].$this->data['User']['phpri3'];
								$res_user['User']['ph_mobile'] =  $this->data['User']['phmob1'].$this->data['User']['phmob2'].$this->data['User']['phmob3'];
								$res_user['User']['contact'] =  $this->data['User']['contact'];
								$res_user['User']['newsletter_status'] =  $this->data['User']['newsletter_status'];
								$res_user['User']['rand'] =  $this->data['User']['rand'];
								$res_user['User']['hash'] =  $hash;
								$this->set('User', $res_user['User']);
								if ($this->User->save($res_user['User'], false))
								{
								$this->Mailer->Subject = "Make Music NY $usertype";
   								$this->Mailer->Message = "Thank you for registering for Make Music New York ".$this->year."!\n\nPlease click here to complete the sign-up process:\n".$url."\n\nHere is your e-mail address and password you signed up with\ne-mail address: ".$email."\nPassword: ".$this->data['User']['password'].$this->emailClose;
   								$this->Mailer->AddAddress($fullname , $email);
    							$this->Mailer->socketmail();
								//$this->flash('Thank you! An Email has been sent to '.$email.' to confirm MMNY '.$usertype, '/users');
								$this->Session->setFlash('<div class="success">Thank you! An e-mail has been sent to '.$email.' to confirm MMNY '.$usertype.'<br />');
								//or <a href="'.$url.'">click here</a> to activate.</div><br>');
								$this->redirect('');
					
								}
							}else{
								$this->Mailer->Subject = "Make Music NY $usertype";
								$this->Mailer->Message = "Thank you for registering for Make Music New York ".$this->year."! \n\nPlease click here to complete the sign-up process:\n".$url."\n\nHere is your e-mail address and password you signed up with\ne-mail address: ".$email."\nPassword: ".$this->data['User']['password'].$this->emailClose;
								$this->Mailer->AddAddress($fullname , $email);
								$this->Mailer->socketmail();
								$this->User->invalidate('emailaddress');
								$this->set('emailaddress_error', 'This address is already registered');	
							}
						}else{
							$res_dupe = $this->data;
							$res_dupe['User']['password'] = md5($this->data['User']['password']);
							if($this->User->save($res_dupe['User'], false))
							{
							$this->Mailer->Subject = "Make Music NY $usertype";
   							$this->Mailer->Message = "Thank you for registering for Make Music New York ".$this->year."! \nPlease click here to complete the sign-up process:\n".$url."\n\nHere is your e-mail address and password you signed up with\ne-mail address: ".$email."\nPassword: ".$this->data['User']['password']."\nThank You,\nMake Music New York ".$this->year."";
   							$this->Mailer->AddAddress($fullname , $email);
    						$this->Mailer->socketmail();
							//$this->flash('Thank you! An Email has been sent to '.$email.' to confirm MMNY '.$usertype, '/users');
							$this->Session->setFlash('<div class="success">Thank you! An e-mail has been sent to '.$email.' to confirm MMNY '.$usertype.'<br />');
							//or <a href="'.$url.'">click here</a> to activate.</div><br>');
							$this->redirect('');
							}
						}
					}else{
						$this->User->invalidate('agree');
						$this->set('agree_error', 'You did not agree to the terms of service.');
					}
					}else{
						$this->User->invalidate('password');
						$this->set('password_error', 'Passwords do not match');
					}
				}else{
					$this->User->invalidate('emailaddress');
					$this->set('emailaddress_error', 'E-mail Addresses do not match');
				}
			}
		}
	}
	function activate()
		{
		$this->layout = 'participate';
		$hash2 = $this->SimpleGet->getValueOf('confirm');
		$res_user = $this->User->findByHash($hash2);
		$this->set('activate','Please Activate your Account');
		$this->set('url','register');
		if ($res_user && $res_user['User']['hash'] == $hash2)
			{
			if ($res_user['User']['status'] == '0')
				{
				$res_user['User']['status'] =  '1';
				$this->set('User', $res_user['User']);
				if ($this->User->save($res_user['User'], false))
					{	
					$this->set('activate','Your account is now active.');
					$this->set('url','login');
					$this->set('gorb','success');
					}			
				}
				else
				{
				$this->set('activate','Your account is already active.');
				$this->set('url','login');
				$this->set('gorb','notice');
				}
			}
			else
			{
			$this->set('activate','Your user account has not be found.');
			$this->set('url','register');	
			$this->set('gorb','error');
			}
		}
	function login() 
	{ 
		$this->layout = 'participate';
		$this->set('activation_error', false); 
		$this->set('type_error', false);
		$this->set('pw_error', false);
		$this->set('password_error', 'Invalid');
		if ($this->data) 
		{ 
			$res_user = $this->User->findByEmailaddress($this->data['User']['emailaddress']);
			$res_password = $this->User->findByPassword(md5($this->data['User']['password']));
			if ($res_user)
			{
				if ($res_user['User']['user_type'] != '140')
				{
					if ($res_user['User']['password'] == md5($this->data['User']['password'])) 
					{ 
						if ($res_user['User']['status'] == '1')
						{
							$this->Session->write('user', $this->data['User']['emailaddress']);
							$this->redirect('/users');
						}
						else
						{ 
							$this->User->invalidate('password');
							$this->set('password_error', 'You have not activated your account.');
							$this->set('activation_error', true);
						}
					}
					else
					{
						$this->User->invalidate('password');
						$this->set('password_error', 'E-mail Address and Password do not match.');
						$this->set('pw_error', true);
					}
				}
				else
				{
					$this->User->invalidate('password');
					$this->set('password_error', 'You have only subscribed to the Newsletter');
					$this->set('type_error', true);
				}
			}
			else
			{
				$this->User->invalidate('password');
				$this->set('password_error', 'E-mail Address and Password do not match.');
				$this->set('pw_error', true);
			}
		}
	} 
	function logout() 
		{ 
		$this->Session->delete('user'); 
		$this->redirect('/users'); 
		} 
	function unsubscribe()
		{ 
		$this->layout = 'participate';
		$this->pageTitle = 'Unsubscribe';
		$this->set('emailaddress_error', 'Invalid Email!');
		if (!empty($this->data))
			{ 
			if ($this->User->validates($this->data))
				{
				if ($this->User->findByEmailaddress($this->data['User']['emailaddress']))
					{
					$res_user = $this->User->findByEmailaddress($this->data['User']['emailaddress']); 
						if ($res_user['User']['newsletter_status'] == '1')
							{
							$res_user['User']['newsletter_status'] =  '0';
							$this->set('User', $res_user['User']);
							if ($this->User->save($res_user['User'], false))
								{
								//$this->flash('Thank you! Unsubscribed!','');
								$this->Session->setFlash('<div class="success">You have been Unsubscribed!</div><br>');
								$this->redirect('');
								}
							}
							else
							{
							$this->User->invalidate('emailaddress');
							$this->set('emailaddress_error', 'Already Unsubscribed.<br><a href="/makemusicny/users/subscribe">Click Here to Subscribe</a>');
							$this->validateErrors($this->User);
							}
						
					}
					else
					{
					$this->User->invalidate('emailaddress');
					$this->set('emailaddress_error', 'E-mail Does Not Exist.<br><a href="/makemusicny/">Signup for MMNY</a>');
					$this->validateErrors($this->User);
					}
				}
			}
		}	
	function edit($id = null)
	{ 
  		$this->checkSession();
		$this->pageTitle = 'Edit';
		$nowuser = $this->Session->read('user');
		$res_user = $this->User->findByEmailaddress($nowuser);
		$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
		$res_location = $this->Location->findByUser_id($res_user['User']['id']);
		$this->set('zip_code_error', 'Invalid Zip Code');
		$this->set('resuser',$res_user['User']);
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Unknown User</div><br>'); 
  			$this->redirect('/users'); 
 		} 
 		if ($id = $res_user['User']['id'])
   		{
   			if (!empty($this->data))
   			{
   				
   				if ($this->User->validates($this->data))
				{
								  	
					$this->cleanUpFields();
					$res_user['User']['salutation'] =  $this->data['User']['salutation'];
   					$res_user['User']['street_add1'] =  $this->data['User']['street_add1'];
					$res_user['User']['street_add2'] =  $this->data['User']['street_add2'];
					$res_user['User']['city'] =  $this->data['User']['city'];
					$res_user['User']['first_name'] =  $this->data['User']['first_name'];
					$res_user['User']['last_name'] =  $this->data['User']['last_name'];
					$res_user['User']['zip_code'] =  $this->data['User']['zip_code'];
					$res_user['User']['state'] =  $this->data['User']['state'];
					$res_user['User']['ph_primary'] =  $this->data['User']['phpri1'].$this->data['User']['phpri2'].$this->data['User']['phpri3'];
					$res_user['User']['ph_mobile'] =  $this->data['User']['phmob1'].$this->data['User']['phmob2'].$this->data['User']['phmob3'];
					$res_user['User']['contact'] =  $this->data['User']['contact'];
					//$res_user['User']['newsletter_status'] =  $this->data['User']['newsletter_status'];
					$this->set('User', $res_user['User']);
					if ($this->User->save($res_user['User'], false))
   					{ 
    					$this->Session->setFlash('<div class="success">User Information Updated.</div><br>'); 
    					$this->redirect('/users'); 
  					}
  				 	else
  					{ 
    					$this->Session->setFlash('<div class="error">Information could not be updated. Please try again later.</div><br>'); 
    					$this->redirect('/users'); 
   					} 
  				}
   			}
   			 
  			if (empty($this->data))
  			{ 
   				$this->data = $this->User->read(null, $id);
  			}
   		}
   		else
   		{
   			$this->Session->setFlash('<div class="error">Unknown User.</div><br>'); 
  			$this->redirect('/users'); 
   		}
 	}
	function view($id = null)
		{ 
		$this->checkSession();
		$this->pageTitle = 'User Information';
		if (!$id)
	  		{ 
   				$this->Session->setFlash('<div class="error">Invalid User.</div><br>'); 
   				$this->redirect('/users');  
  			} 
  			$this->set('has_artist', true);
			$this->set('has_location', true);
  			$res_user = $this->User->read(null, $id); 
			$this->set('User', $res_user);
			$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
			$this->set('Artist', $res_artist['Artist']);
			$res_location = $this->Location->findByUser_id($res_user['User']['id']);
			$this->set('Location', $res_location['Location']);
			if ($res_artist)
				{
					$this->set('Artist', $res_artist['Artist']);
					$this->set('has_artist', true);
				}
			if ($res_location)
				{
					$this->set('Location', $res_location['Location']);
					$this->set('has_location', true);
				}
 		}	
	function reset()
		{ 
			$this->layout = 'participate';
			$this->pageTitle = 'Reset Password';
			$this->set('emailaddress_error', 'Invalid Email!');
			if (!empty($this->data))
			{ 
				if ($this->User->validates($this->data))
				{
					if ($this->User->findByEmailaddress($this->data['User']['emailaddress']))
					{
						$res_user = $this->User->findByEmailaddress($this->data['User']['emailaddress']); 
						if ($res_user['User']['user_type'] == '140')
						{
							$this->User->invalidate('emailaddress');
							$this->set('emailaddress_error', 'You are only signed up for the Newsletter. <p><a href="/makemusicny/users/register">Click Here to Register</a></p>');
							$this->validateErrors($this->User);
						}
						else
						{
							$email = $res_user['User']['emailaddress'];
			  				$fname = $res_user['User']['first_name'];
			  				$lname = $res_user['User']['last_name'];
		  					$fullname = $fname.' '.$lname;
		  					$hash = $res_user['User']['hash'];
		  					$url = 'http://'.APP_DOMAIN_LOCATION.'/users/password/confirm/'.$hash;
							$this->Mailer->Subject = "Make Music NY Password Reset Request";
   							$this->Mailer->Message = "Please click here to reset your password and create a new one:\n".$url.$this->emailClose;
   							$this->Mailer->AddAddress($fullname , $email);
   							$this->Mailer->socketmail();
							//$this->flash('An Email has been sent to '.$email.' with further information on how to reset your password.', '/users/');
							$this->Session->setFlash($this->Mailer->errorMsg.'<div class="success">An e-mail has been sent to '.$email.' with further information on how to reset your password.</div><br>');
								//'<div class="success">An e-mail has been sent to '.$email.' with further information on how to reset your password.</div><br>'); 
	    					$this->redirect('/users/login'); 		  										
						}
					}
					else
					{
						$this->User->invalidate('emailaddress');
						$this->set('emailaddress_error', 'E-mail Does Not Exist. <p><a href="/makemusicny/participate">Signup for MMNY</a></p>');
						$this->validateErrors($this->User);
					}
				}
			}
		}	
	function password()
		{
			$this->layout = 'participate';
			$hash2 = $this->SimpleGet->getValueOf('confirm');
			$res_user = $this->User->findByHash($hash2);
			$this->set('yesuser',false);
			$this->set('password_error','Invalid');
			$this->set('User', $res_user['User']);
			if ($res_user && $res_user['User']['hash'] == $hash2)
			{
				$this->set('yesuser',true);
				$email = $res_user['User']['emailaddress'];
		  		$fname = $res_user['User']['first_name'];
		  		$lname = $res_user['User']['last_name'];
		  		$fullname = $fname.' '.$lname;
		  		$hash = $res_user['User']['hash'];
		  		$url = 'http://'.APP_DOMAIN_LOCATION.'/users/activate/confirm/'.$hash;
		  		if (!empty($this->data))
				{
					if ($this->User->validates($this->data))
					{
		  			if ($this->data['User']['password'] == $this->data['User']['password2'])
					{						
						$res_user['User']['password'] =  md5($this->data['User']['password']);
						$this->set('User', $res_user['User']);
						if ($this->User->save($res_user['User'], false))
						{
							$this->Mailer->Subject = "Make Music NY Password Reset Complete";
   							$this->Mailer->Message = "Your password has been reset\n\nHere is your e-mail address and NEW password.\ne-mail address: ".$email."\nPassword: ".$this->data['User']['password']."\n\nIf you have still not activated your account, you can do so here:\n".$url.$this->emailClose;
   							$this->Mailer->AddAddress($fullname , $email);
    						$this->Mailer->socketmail();
							//$this->flash('An Email has been sent to '.$email.' with your new login information','');
							$this->Session->setFlash('<div class="success">An e-mail has been sent to '.$email.' with your new login information.</div><br>'); 
	    					$this->redirect('/users'); 		  
						}
						else
						{
							//$this->flash('Could not save information.  Please try again', '/users/password/confirm/'.$hash2);
							$this->Session->setFlash('<div class="error">Could not save information. Please try again.</div><br>'); 
				    		$this->redirect('/users/password/confirm/'.$hash2); 	
								
						}
					}
					else
					{
						$this->User->invalidate('password');
						$this->set('password_error', 'Passwords do not match');
					}
					}
				}
			}
			else
			{
				//$this->flash('User Information Not Found.  Please try again', '');
				$this->Session->setFlash('<div class="error">User Information Not Found. Please try again.</div><br>'); 
	    		$this->redirect(''); 				}
		}		
	function index() 
		{                      
			$this->checkSession();
			$nowuser = $this->Session->read('user');
			if ($nowuser) 
			{ 
				$res_user = $this->User->findByEmailaddress($nowuser); 
				$this->set('User', $res_user['User']);
				$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
				$this->set('Artist', $res_artist['Artist']);
				$res_location = $this->Location->findByUser_id($res_user['User']['id']);
				$this->set('Location', $res_location['Location']);                        
				
				//$this->Pagination->show = 100;
				$criteria=$res_user['User']['id'];
				$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
			    //print_r( $data );
				//$this->set('data',$data);	
				$locationArray = array();   
				foreach ($data as $output)  {
					foreach ($output['Location'] as $k=>$v)  {  
						//echo ( $k."=".$v."<br />" ); 
						if ($k == "id") {
					    	$locationArray[] = $v;  
						}
					}
				}   
				//echo count($locationArray);
				$this->set('Locations',$this->locations($res_user['User']['id'], 10)); 
				$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.acl' => '0','Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 
					'Performance.location_id' => $locationArray)));
				$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'])));
				$this->set('pending_locations',$this->Performance->findAll(array('Performance.acl' => '0',
					'Performance.location_id' => $locationArray,
					"AND" => array("or" => array('Performance.artist_confirmed' => '0', 'Performance.admin_confirmed' => '0'	)))));
			   	$this->set('pending_artists',$this->Performance->findAll(array('Performance.lca' => '0',
					'Performance.artist_id' => $res_artist['Artist']['id'],
					'Performance.artist_confirmed' => '1', "AND" => array("or" => array('Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')))));
				$this->set('your_locations',$this->Performance->findAll(array('Performance.location_id' => $res_location['Location']['id'], 'Performance.location_confirmed' => '0')));
				$this->set('your_artists',$this->Performance->findAll(array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.artist_confirmed' => '0')));
				if ($res_user['User']['user_type'] == '220')
				{
					$this->redirect('/admin/users/'); 
				}
			}
			else
			{ 
				$this->redirect('/users'); 
			}
		}
	function artist() 
		{ 
			$this->checkSession();
			$nowuser = $this->Session->read('user'); 
			if ($nowuser) 
			{ 
				$res_user = $this->User->findByEmailaddress($nowuser); 
				$this->set('User', $res_user['User']);
				$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
				$this->set('Artist', $res_artist['Artist']);
				$res_location = $this->Location->findByUser_id($res_user['User']['id']);
				$this->set('Location', $res_location['Location']);
				$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.location_id' => $res_location['Location']['id'])));
				$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'])));
				$this->set('pending_locations',$this->Performance->findAll(array('Performance.acl' => '0','Performance.location_id' => $res_location['Location']['id'],'Performance.location_confirmed' => '1', "AND" => array("or" => array('Performance.artist_confirmed' => '0', 'Performance.admin_confirmed' => '0'	)))));
				$this->set('pending_artists',$this->Performance->findAll(array('Performance.lca' => '0','Performance.artist_id' => $res_artist['Artist']['id'],'Performance.artist_confirmed' => '1', "AND" => array("or" => array('Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')))));
				$this->set('your_locations',$this->Performance->findAll(array('Performance.location_id' => $res_location['Location']['id'], 'Performance.location_confirmed' => '0')));
				$this->set('your_artists',$this->Performance->findAll(array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.artist_confirmed' => '0')));
			}
			else
			{ 
				$this->redirect('/users'); 
			}
		}
	function location() 
		{ 
			$this->checkSession();
			$nowuser = $this->Session->read('user'); 
			if ($nowuser) 
			{ 
				$res_user = $this->User->findByEmailaddress($nowuser); 
				$this->set('User', $res_user['User']);
				$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
				$this->set('Artist', $res_artist['Artist']);
				$res_location = $this->Location->findByUser_id($res_user['User']['id']);
				$this->set('Location', $res_location['Location']);    
				
				//$this->Pagination->show = 100;
				$criteria=$res_user['User']['id'];
				$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
			    //print_r( $data );
				//$this->set('data',$data);	
				$locationArray = array();   
				foreach ($data as $output)  {
					foreach ($output['Location'] as $k=>$v)  {  
						//echo ( $k."=".$v."<br />" ); 
						if ($k == "id") {
					    	$locationArray[] = $v;  
						}
					}
				}   
				
				
				$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.acl' => '0','Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 
					'Performance.location_id' => $locationArray)));
				
				$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'])));
				$this->set('pending_locations',$this->Performance->findAll(array('Performance.acl' => '0',
					'Performance.location_id' => $locationArray,'Performance.location_confirmed' => '1', "AND" => array("or" => array('Performance.artist_confirmed' => '0', 'Performance.admin_confirmed' => '0'	)))));
				$this->set('pending_artists',$this->Performance->findAll(array('Performance.lca' => '0','Performance.artist_id' => $res_artist['Artist']['id'],'Performance.artist_confirmed' => '1', "AND" => array("or" => array('Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')))));
				$this->set('your_locations',$this->Performance->findAll(array('Performance.location_id' => $res_location['Location']['id'], 'Performance.location_confirmed' => '0')));
				$this->set('your_artists',$this->Performance->findAll(array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.artist_confirmed' => '0')));
			}
			else
			{ 
				$this->redirect('/users'); 
			}
		}
		function listall() 
			{	                     
 				//$this->layout = 'listall';
				$this->pageTitle = 'My Locations';
   		   		// print_r( $_SERVER );
				$this->checkSession();
				$nowuser = $this->Session->read('user'); 
				if ($nowuser) 
				{ 
					$this->Pagination->show = 10;
	 				$res_user = $this->User->findByEmailaddress($nowuser); 
					$this->set('User', $res_user['User']);
					$criteria['Location']['user_id'] = "= ".$res_user['User']['id']."";
					list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Location'));  
					$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC', $limit, $page);
				    //print_r( $data );
					$this->set('data',$data);				 
				}  
				else
				{ 
					$this->redirect('/users'); 
				}
			}
	function confirmed() 
		{ 
			$this->checkSession();
			$nowuser = $this->Session->read('user'); 
			if ($nowuser) 
			{ 
				$res_user = $this->User->findByEmailaddress($nowuser); 
				$this->set('User', $res_user['User']);
				$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
				$this->set('Artist', $res_artist['Artist']);
				$res_location = $this->Location->findByUser_id($res_user['User']['id']);   
				
				$criteria=$res_user['User']['id'];
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
				
				
 				$this->set('Locations',$this->locations($res_user['User']['id'], 10)); 
   				$this->set('Location', $res_location['Location']);
				$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.acl' => '0','Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 
					'Performance.location_id' => $locationArray)));
				$this->set('confirmed_artists',$this->Performance->findAll(array('Performance.lca' => '0','Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.artist_id' => $res_artist['Artist']['id'])));
				$this->set('pending_locations',$this->Performance->findAll(array('Performance.acl' => '0',
					'Performance.location_id' => $locationArray,'Performance.location_confirmed' => '1', "AND" => array("or" => array('Performance.artist_confirmed' => '0', 'Performance.admin_confirmed' => '0'	)))));
				$this->set('pending_artists',$this->Performance->findAll(array('Performance.lca' => '0','Performance.artist_id' => $res_artist['Artist']['id'],'Performance.artist_confirmed' => '1', "AND" => array("or" => array('Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')))));
				$this->set('your_locations',$this->Performance->findAll(array(
					'Performance.location_id' => $locationArray, 'Performance.location_confirmed' => '1')));
				$this->set('your_artists',$this->Performance->findAll(array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.artist_confirmed' => '0')));
				if ($res_user['User']['user_type'] == '220')
				{
					$this->redirect('/admin/users/confirmed'); 
				}
			}
			else
			{ 
				$this->redirect('/users'); 
			}
		}
	function pending() 
		{           
			$this->checkSession();
			$nowuser = $this->Session->read('user'); 
			if ($nowuser) 
			{ 
				$res_user = $this->User->findByEmailaddress($nowuser); 
				$this->set('User', $res_user['User']);
				$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
				$this->set('Artist', $res_artist['Artist']);
				
				$this->Pagination->show = 100;
				$criteria=$res_user['User']['id'];
				$data = $this->Location->findAllByUser_id($criteria, '', 'locationname ASC');
			    //print_r( $data );			//$this->set('data',$data);	
				$locationArray = array();   
				foreach ($data as $output)  {
					foreach ($output['Location'] as $k=>$v)  {  
						//echo ( $k."=".$v."<br />" ); 
						if ($k == "id") {
					    	$locationArray[] = $v;  
						}
					}
				}   
				
				$this->set('Locations',$this->locations($res_user['User']['id'], 10)); 
					$res_location = $this->Location->findByUser_id($res_user['User']['id']);
				$this->set('confirmed_locations',$this->Artist->Performance->findAll(
					array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 
					'Performance.location_id' => $locationArray)));
				$this->set('confirmed_artists',$this->Performance->findAll(
					array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 
					'Performance.artist_id' => $res_artist['Artist']['id'])));
				$this->set('pending_locations',$this->Performance->findAll(
					array('Performance.acl' => '0',
					'Performance.location_id' => $locationArray,
					'Performance.location_confirmed' => '1', 
					"AND" => array("or" => array('Performance.artist_confirmed' => '0', 'Performance.admin_confirmed' => '0'	)))));
				$this->set('pending_artists',$this->Performance->findAll(
					array('Performance.lca' => '0','Performance.artist_id' => $res_artist['Artist']['id'],'Performance.artist_confirmed' => '1', "AND" => array("or" => array('Performance.location_confirmed' => '0', 'Performance.admin_confirmed' => '0')))));
				$this->set('your_locations',$this->Performance->findAll(
					array('Performance.location_id' => 
					$locationArray, 'Performance.location_confirmed' => '0')));
				$this->set('your_artists',$this->Performance->findAll(
					array('Performance.artist_id' => $res_artist['Artist']['id'], 'Performance.artist_confirmed' => '0')));
				//
				if ($res_user['User']['user_type'] == '220')
				{
					$this->redirect('/admin/users/pending'); 
				}
			}
			else
			{ 
				$this->redirect('/users'); 
			}
		}
	function ownartist()
	{
		$this->checkSession();
		$this->pageTitle = 'Book';
		$nowuser = $this->Session->read('user');
		$res_user = $this->User->findByEmailaddress($nowuser);
		$res_location = $this->Location->findByUser_id($res_user['User']['id']); 
		$this->Pagination->resultsPerPage = array(10);
		list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Performance'));
		
		$this->set('Locations',$this->locations($res_user['User']['id'], 1000)); 
		
		$this->set('Location', $res_location['Location']);
		$this->set('User',$res_user['User']);
		$this->set('create', false);
		$email = $res_user['User']['emailaddress'];
		$fname = $res_user['User']['first_name'];
		$lname = $res_user['User']['last_name'];
		$fullname = $fname.' '.$lname;
		if (!$res_location)
		{
			$this->Session->setFlash('<div class="error">Please Create Your Location First<br><a href="/makemusicny/location/create">Click Here to create your Location</a></div><br>'); 
  			$this->redirect('/users/');  
		}
		if (!empty($this->data))
		{
			if ($this->Artist->findByGroupname($this->data['Artist']['groupname']))
			{
				$this->Artist->invalidate('groupname');
				$this->set('groupname_error', 'Name has already been created');
			}
			if ($this->Artist->validates($this->data) && $this->Performance->validates($this->data))
			{
				if ($nowuser) 
				{ 
					if ($res_user['User']['user_type'] == '160')
					{
						$this->Session->setFlash('<div class="error">Artist cannot play Artist</div><br>'); 
  						$this->redirect('/users/');  
					}
					else
					{
							$userid = $res_user['User']['id'];
							$this->data['Artist']['user_id'] = $userid;
							$this->data['Artist']['lca'] = '1';
							$genre1 = $this->data['Artist']['genre1'];
							$genre2 = $this->data['Artist']['genre2'];
							$genre3 = $this->data['Artist']['genre3'];
							$this->data['Artist'][$genre1] = "1";
							$this->data['Artist'][$genre2] = "1";
							$this->data['Artist'][$genre3] = "1";
							if (empty($genre1) && empty($genre2) && empty($genre3))
							{
								$this->data['Artist']['other'] = "1";
							}
							
							if ($this->Artist->save($this->data))
							{      
								//print_r($this->data);
								$this->data['Performance']['location_id'] = $this->data['Performance']['location_id'];
								$this->data['Performance']['location_confirmed'] == '1';
								$this->data['Performance']['artist_confirmed'] == '1';
								$artistid = $this->Artist->findByGroupname($this->data['Artist']['groupname']);
								$this->data['Performance']['artist_id'] = $this->Artist->id;
								$this->data['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
								$this->data['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
								$this->data['Performance']['lca'] = '1';
							    //print_r($this->data['Performance']);
								if ($this->Performance->save($this->data))
								{
								    $this->Session->setFlash('<div class="success">Your Performance was saved and submitted to MMNY for approval.<br>You will be notified upon approval or denial.</div><br>');
								    $this->redirect('/users/pending');  
								}
								else
								{
									$this->Session->setFlash('<div class="error">The Performance could not be saved. Please, try again.</div><br>');
									$this->redirect('/users'); 
								}								
							}
				
					}
				}
			}
		}
	}
	function ownlocation()
	{
		$this->checkSession();
		$this->pageTitle = 'Book';
		$nowuser = $this->Session->read('user');
		$res_user = $this->User->findByEmailaddress($nowuser);
		$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
		$this->set('Artist', $res_artist['Artist']);
		$this->set('User',$res_user['User']);
		$this->set('create', false);        
		$this->set('locationname_error', '');
		$email = $res_user['User']['emailaddress'];
		$fname = $res_user['User']['first_name'];
		$lname = $res_user['User']['last_name'];
		$fullname = $fname.' '.$lname;
		if (!$res_artist)
		{
			$this->Session->setFlash('<div class="error">Please Create Your Artist First.<br><a href="/participate/artists/create">Click Here to create your Artist</a></div><br>'); 
  			$this->redirect('/users/');  
		}
		if (!empty($this->data))
		{
  			if ($this->Location->findByLocationname($this->data['Location']['locationname']))
			{
				$this->Location->invalidate('locationname');
				$this->set('locationname_error', 'Name has already been created');
			}
		    if (empty($this->params['form']['hood']))             
		    {
			  	//$this->Session->setFlash('<div class="error">Please select a neighborhood and retry.</div><br>'); 
				//$this->redirect('/users/location');  
			   	$this->Location->invalidate('hood');  
				//echo $this->data['Location']['hood']."asdasd";
		    }
			if ($this->Location->validates($this->data) && $this->Performance->validates($this->data))
			{
				if ($nowuser) 
				{ 
					if ($res_user['User']['user_type'] == '180')
					{
						$this->Session->setFlash('<div class="error">Location cannot play Location</div><br>'); 
  						$this->redirect('/users/');  
					}
					else
					{

						$userid = $res_user['User']['id'];
							$this->data['Location']['user_id'] = $userid;
							$this->data['Location']['acl'] = '1';
							$this->data['Location']['other'] = '1';
							$this->data['Location']['hood'] = $this->params['form']['hood'];
							if ($this->Location->save($this->data))
							{
								$this->data['Performance']['artist_id'] = $res_artist['Artist']['id'];
								$this->data['Performance']['location_confirmed'] == '1';
								$this->data['Performance']['artist_confirmed'] == '1';
								$locationid = $this->Location->findByLocationname($this->data['Location']['locationname']);
								$this->data['Performance']['location_id'] = $this->Location->id;
								$this->data['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
								$this->data['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
								$this->data['Performance']['acl'] = '1';
								if ($this->Performance->save($this->data))
								{
									$this->Session->setFlash('<div class="success">Your Performance was saved and submitted to MMNY for approval.<br>You will be notified upon approval or denial.</div><br>');
									$this->redirect('/users');  
								}
								else
								{
									$this->Session->setFlash('<div class="error">The Performance could not be saved. Please, try again.</div><br>');
									$this->redirect('/users'); 
								}								
							}
				
					}
				}
			}
		}
	}

	function admin_confirmed()
	{
		$this->checkSession();

		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$this->set('all_confirmed',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1')));
		$this->set('await_admin',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '0')));
		$criteria= array('Performance.location_confirmed' => '1','Performance.artist_confirmed' => '1','Performance.admin_confirmed' => '1');
		$this->Pagination->resultsPerPage = array(10);
		list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Performance'));
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated DESC', $limit, $page);
		$this->set('data',$data);  
	}
	function admin_pending()
	{
		$this->checkSession();

		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$this->set('all_confirmed',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1')));
		$this->set('await_admin',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '0')));
		$criteria= array('Performance.location_confirmed' => '1','Performance.artist_confirmed' => '1','Performance.admin_confirmed' => '0');
		$this->Pagination->resultsPerPage = array(10);
		list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Performance'));
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated ASC', $limit, $page);
		$this->set('data',$data);  
	}	
	function admin_penartist()
	{
		$this->checkSession();

		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$this->set('all_confirmed',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1')));
		$this->set('await_artist',$this->Performance->findAll(array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '1')));
		$this->set('await_admin',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '0')));
		$criteria= array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '1');
		$this->Pagination->resultsPerPage = array(10);
		list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Performance'));
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated ASC', $limit, $page);
		$this->set('data',$data);  
	}	
	function admin_penlocation()
	{
		$this->checkSession();

		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$this->set('all_confirmed',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1')));
		$this->set('await_admin',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '0')));
		$this->set('await_location',$this->Performance->findAll(array('Performance.location_confirmed' => '0','Performance.artist_confirmed' => '1')));
		$criteria= array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '0');
		$this->Pagination->resultsPerPage = array(10);
		list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Performance'));
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated ASC', $limit, $page);
		$this->set('data',$data);  	
	}
	function admin_penboth()
	{
		$this->checkSession();

		$this->pageTitle = 'Confirm';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$this->set('all_confirmed',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1')));
		$this->set('await_both',$this->Performance->findAll(array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '0')));
		$this->set('await_admin',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '0')));
		$criteria= array('Performance.artist_confirmed' => '0','Performance.location_confirmed' => '0');
		$this->Pagination->resultsPerPage = array(10);
		list($order,$limit,$page) = $this->Pagination->init($criteria,'',array('modelClass'=>'Performance'));
		$data = $this->Performance->findAll($criteria, '', 'Performance.last_updated ASC', $limit, $page);
		$this->set('data',$data);  
	}			
	function admin_dlcsv()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();
		$data = $this->User->findAll('', array('*'), 'emailaddress ASC');
		$this->set('data', $data);
	}	
	function admin_csv()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();
		$data = $this->User->findAll('', array('*'), 'emailaddress ASC');
		$this->set('data', $data);
	}
 	function admin_csvlocation()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();                         
		$data = $this->Location->findAll('', array('*'), 'locationname ASC');
		$this->set('data', $data);
	}   
	function admin_dlcsvlocation()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();
		$data = $this->Location->findAll('', array('*'), 'locationname ASC');
		$this->set('data', $data);
	}	
	
 	function admin_csvartist()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();                         
	   
	 	//$data = $this->Artist->findAll('', array('*'), 'groupname ASC');        
		$criteria = array( 'rock' => $_GET['rock']);
		$data = $this->Artist->findAll($criteria, '', 'groupname ASC');
		
		$this->set('data', $data);
	}
	function admin_dlcsvartist()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();
		$criteria = array( 'rock' => $_GET['rock']);
		$data = $this->Artist->findAll($criteria, '', 'groupname ASC');
		$this->set('data', $data);
	}	
 	function admin_csvperformance()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();                         
		$criteria = array( 'Performance.location_confirmed' => $_GET['lc']);
		$data = $this->Performance->findAll($criteria, '', 'Performance.id DESC');
		$this->set('data', $data);
	}
	function admin_dlcsvperformance()
	{
		$this->checkSession();

		$this->pageTitle = 'CSV';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$data = array();
		$criteria = array( 'Performance.location_confirmed' => $_GET['lc']);
		$data = $this->Performance->findAll($criteria, '', 'Performance.id DESC');
		$this->set('data', $data);
	}	
    function admin_index()
	{
		$this->checkSession();

		$this->pageTitle = 'Home';
		$now_user = $this->User->findByEmailaddress($this->Session->read('user'));
		$this->set('User', $now_user);
		if ($now_user['User']['user_type'] != '220')
		{
			$this->redirect('/users'); 
		}
		$this->set('all_confirmed',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '1')));
		$this->set('await_admin',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1','Performance.admin_confirmed' => '0')));
	}
	function locations($id = null, $lim) {  
		$locationArray = array();
		$criteria=$id;
		list($order,$limit,$page) = $this->Pagination->init($criteria);   
		if ($lim) $limit = $lim;
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