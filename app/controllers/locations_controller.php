<?php
class LocationsController extends AppController
{

	var $name = 'Locations';
	var $uses = array('Location', 'User','Performance','Artist');
	var $components = array('SimpleGet', 'Mailer', 'Pagination','RequestHandler');
	var $helpers = array('Pagination','ajax', 'Javascript');
	var $layout = 'locations';
	var $emailClose = "\n\nThank You,\nMake Music New York 2012";
	 
	//var $ppg = 20;
function create()
	{                              
    	
		$this->layout = 'myaccount';
		$this->checkSession();
		$this->set('locationname_error', '<div class="error">Sorry, that location name is already taken.<br><a href="/participate/locations">Click Here</a></div><br>');
		$this->set('hood_error', '<div class="error">Please select a neighborhood and retry.</div><br>');
		$this->pageTitle = 'Create';
		if (!empty($this->data))
		{
			if ($this->Location->validates($this->data))
			{
				$nowuser = $this->Session->read('user'); 
				if ($nowuser) 
				{ 
					$res_user = $this->User->findByEmailaddress($nowuser); 
					$this->set('User', $res_user['User']);
					if ($this->Location->findByUser_id($res_user['User']['id']))
					{
						$this->Session->setFlash('<div class="error">You have already created a Location.<br><a href="/newyork/makemusicny/locations">Click Here to find Artist to perform at your Location</a></div><br>'); 
  						$this->redirect('/users/location');  
					}
					else
					{
						if ($res_user['User']['user_type'] == '160')   // EDITED COLIN KENNEDY VALUE WAS = 160 CHANGED TO != 161
						{
						 $this->Session->setFlash('<div class="notice">You registered as an artist</div><br>'); 
  							$this->redirect('/artists/create');  
						}
						else
						{
							if ($this->Location->findByLocationname($this->data['Location']['locationname']))             
							{
								$this->Session->setFlash('<div class="error">Sorry, that location name is already taken.<br><a href="/participate/locations">Click Here</a></div><br>'); 
		  						//$this->redirect('/users/location');  
								$this->Location->invalidate('locationname');
							}
						    else if (empty($this->params['form']['hood']))             
						    {
							  	//$this->Session->setFlash('<div class="error">Please select a neighborhood and retry.</div><br>'); 
		  						//$this->redirect('/users/location');  
							   	$this->Location->invalidate('hood');  
								//echo $this->data['Location']['hood']."asdasd";
						    }
							else
							{
								$this->data['Location']['ph_primary'] = $this->data['Location']['phpri1'].$this->data['Location']['phpri2'].$this->data['Location']['phpri3'];
								$this->data['Location']['onsite_phone'] = $this->data['Location']['photh1'].$this->data['Location']['photh2'].$this->data['Location']['photh3'];
								$userid = $res_user['User']['id'];
								$this->data['Location']['user_id'] = $userid;
								$genre1 = $this->data['Location']['genre1'];
								$genre2 = $this->data['Location']['genre2'];
								$genre3 = $this->data['Location']['genre3'];
								$this->data['Location'][$genre1] = "1";
								$this->data['Location'][$genre2] = "1";
								$this->data['Location'][$genre3] = "1";
								if (empty($genre1) && empty($genre2) && empty($genre3))
								{
									$this->data['Location']['other'] = "1";
								}
								$this->data['Location']['hood'] = $this->params['form']['hood'];
								if ($this->Location->save($this->data))
								{
									$this->Session->setFlash('<div class="success">Your Location has been saved. You now have the following options:<br><a href="/participate/artists/">Find an artist to perform at your location</a><br><a href="/participate/users/ownartist/">Create your own performance at your location</a></div><br>');
  									$this->redirect('/users/location');  
								}
								else
								{
									$this->Session->setFlash('<div class="error">The Location could not be saved. Please, try again.</div><br>');
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
				$this->Session->setFlash('<div class="error">The Location could not be saved. Please, try again.</div><br>');   //print_r($this);
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
		if (strlen(str_replace("/participate/locations/edit/", "", $_SERVER['REQUEST_URI'])) >1) {
		    //print_r(str_replace("/participate/locations/edit/", "", $_SERVER['REQUEST_URI']));  
		    $id = str_replace("/participate/locations/edit/", "", $_SERVER['REQUEST_URI']);
			$id = str_replace("participate/", "", $id);        
			$res_location = $this->Location->findById(str_replace("/participate/locations/edit/", "", $_SERVER['REQUEST_URI']));   
        	if ($res_location['Location']['user_id'] != $res_user['User']['id'])  {
	   			$this->Session->setFlash('<div class="error">Invalid Itema</div><br>'); 
	  			$this->redirect('/users'); 	
			}	
   		} else {   
			$res_location = $this->Location->findByUser_id($res_user['User']['id']); 
		}
		$this->set('resuser',$res_location['Location']);
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Invalid Itemb</div><br>'); 
  			$this->redirect('/users'); 
 		}          
 		if ($id = $res_location['Location']['id'])
   		{ 		
	 		if (!empty($this->data))
   			{
				//echo "$id = ".$res_location['Location']['id'];
			    if (empty($this->params['form']['hood']))             
			    {
				  	//$this->Session->setFlash('<div class="error">Please select a neighborhood and retry.</div><br>'); 
					//$this->redirect('/users/location');  
				   	$this->Location->invalidate('hood');  
					//echo $this->data['Location']['hood']."asdasd";
			    }
				//else if ($this->Location->findByLocationname($this->data['Location']['locationname']))             
				//{
				 //   $this->Session->setFlash('<div class="error">Sorry, that location name is already taken.<br><a href="/participate/locations">Click Here</a></div><br>'); 
				    //$this->redirect('/users/location');  
					//$this->Location->invalidate('locationname');
				//}
			
				else if ($this->Location->validates($this->data))
				{		
   					$this->cleanUpFields(); 
	   				$res_location['Location']['type'] =  $this->data['Location']['type'];
					$res_location['Location']['locationname'] =  $this->data['Location']['locationname'];
					$res_location['Location']['website'] =  $this->data['Location']['website'];
					$res_location['Location']['street_add1'] =  $this->data['Location']['street_add1'];
					$res_location['Location']['street_add2'] =  $this->data['Location']['street_add2'];
					$res_location['Location']['city'] =  $this->data['Location']['city'];
					$res_location['Location']['state'] =  $this->data['Location']['state'];
					$res_location['Location']['zip_code'] =  $this->data['Location']['zip_code'];
					$res_location['Location']['hood'] =  $this->params['form']['hood'];
					$res_location['Location']['ph_primary'] = $this->data['Location']['phpri1'].$this->data['Location']['phpri2'].$this->data['Location']['phpri3'];
				
					$res_location['Location']['blues'] =  "0";
					$res_location['Location']['cabaret'] =  "0";
					$res_location['Location']['classical'] =  "0";
					$res_location['Location']['country'] =  "0";
					$res_location['Location']['electronic'] =  "0";
					$res_location['Location']['experimental'] =  "0";
					$res_location['Location']['folk'] =  "0";
					$res_location['Location']['hiphop'] =  "0";
					$res_location['Location']['jazz'] =  "0";
					$res_location['Location']['kids'] =  "0";
					$res_location['Location']['latin'] =  "0";
					$res_location['Location']['opera'] =  "0";
					$res_location['Location']['pop'] =  "0";
					$res_location['Location']['reggae'] =  "0";
					$res_location['Location']['religious'] =  "0";
					$res_location['Location']['rock'] =  "0";
					$res_location['Location']['soul'] =  "0";
					$res_location['Location']['standards'] =  "0";
					$res_location['Location']['world'] =  "0";
					$res_location['Location']['other'] =  "0";
				
					$res_location['Location']['genre1'] = $this->data['Location']['genre1'];
					$res_location['Location']['genre2'] = $this->data['Location']['genre2'];
					$res_location['Location']['genre3'] = $this->data['Location']['genre3'];
				
					$res_location['Location']['description'] =  $this->data['Location']['description'];
					$res_location['Location']['promote'] =  $this->data['Location']['promote'];
					$res_location['Location']['poster'] =  $this->data['Location']['poster'];				
					$res_location['Location']['electricity'] =  $this->data['Location']['electricity'];
					$res_location['Location']['time_midday'] =  $this->data['Location']['time_midday'];
					$res_location['Location']['time_afternoon'] =  $this->data['Location']['time_afternoon'];
					$res_location['Location']['time_lateafter'] =  $this->data['Location']['time_lateafter'];
					$res_location['Location']['time_evening'] =  $this->data['Location']['time_evening'];
					$res_location['Location']['surrounding'] =  $this->data['Location']['surrounding'];
					$res_location['Location']['rain'] =  $this->data['Location']['rain'];
					$res_location['Location']['rain_other'] =  $this->data['Location']['rain_other'];								
					$res_location['Location']['onsite'] =  $this->data['Location']['onsite'];
					$res_location['Location']['onsite_name'] =  $this->data['Location']['onsite_name'];
					$res_location['Location']['onsite_phone'] = $this->data['Location']['photh1'].$this->data['Location']['photh2'].$this->data['Location']['photh3'];
				
					$genre1 = $this->data['Location']['genre1'];
					$genre2 = $this->data['Location']['genre2'];
					$genre3 = $this->data['Location']['genre3'];
					$res_location['Location'][$genre1] = "1";
					$res_location['Location'][$genre2] = "1";
					$res_location['Location'][$genre3] = "1";
					if (empty($genre1) && empty($genre2) && empty($genre3))
					{
						$res_location['Location']['other'] = "1";
					}
				
				    if (strlen(str_replace("/participate/locations/edit/", "", $_SERVER['REQUEST_URI'])) >1) {
						$res_location['Location']['id'] = $id;
					}
					$this->set('Location', $res_location['Location']);           
					$this->Location->id = $id;
					//print_r($res_location['Location']);    
					//die;
					if ($this->Location->save($res_location['Location'], false))
	   				{ 
	    				$this->Session->setFlash('<div class="success">Location Information Updated</div><br>'); 
		    			$this->redirect('/users/'); 
	  				}
	  				 else
	  				{ 
	    				$this->Session->setFlash('<div class="error">Location information could not be updated - Please try again later</div><br>'); 
	   				} 
  			
	  			}
	 		}
	  		if (empty($this->data))
  			{ 
   				$this->data = $this->Location->read(null, $id);
  			}
   		}
   		else
   		{
   		   	$this->Session->setFlash('<div class="success">Invalid Location</div><br>'); 
  			//$this->redirect('/users');  
   		}
 	}
function oldview($id = null)
	{ 
		$this->checkSession();
		
		$this->pageTitle = 'Location Information';
		if (!$id)
	  		{ 
   				$this->Session->setFlash('Invalid Location.'); 
   				$this->redirect('/artist');  
  			} 
  			$res_location = $this->Location->read(null, $id); 
			$res_user = $this->User->findById($res_location['Location']['user_id']);
			$this->set('Location', $res_location['Location']);
			$this->set('User', $res_user);	
 		}	
function index() 
	{	 
		$this->checkSession();
		$this->Pagination->show = 20;

		$criteria=NULL;
		//$this->Pagination->resultsPerPage = array($this->ppg);  
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Location->findAll($criteria, '', 'locationname ASC', $limit, $page);
		$this->set('data',$data); 
	}
function sort()
	{
		$this->checkSession();
		$this->Pagination->show = 20;
		
		$genre = $this->SimpleGet->getValueOf('genres');
		$this->set('genre', $genre); 
		
		$smlhood = $this->SimpleGet->getValueOf('neighborhoods');
		$this->set('smlhood', $smlhood);
		
		$smltype = $this->SimpleGet->getValueOf('types');
		$this->set('smltype', $smltype);
		
		$electric = $this->SimpleGet->getValueOf('electricity');
		$this->set('electricity', $electric); 

	   // $this->Pagination->resultsPerPage = array($this->ppg); 
 		$this->Pagination->show = 20;

	   // if (isset($this->params['url']['perpage']) == "all") {
	   //   $this->Pagination->resultsPerPage = array(1000);  
	   // }  

		if ($genre)
		{
		$locationgenre = "Location.".$genre;
		$criteria=array($locationgenre => '1');
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Location->findAll($criteria, '', 'locationname ASC', $limit, $page);
		$this->set('data',$data); 
		//$this->set('knownlocations',$this->Location->findAll(array($locationgenre => '1'),'','locationname ASC'));
		}  
		
		if ($electric > -1)
		{                        
 		$criteria=array('Location.electricity' => $electric);
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Location->findAll($criteria, '', 'locationname ASC', $limit, $page);
		$this->set('data',$data); 
		//$this->set('knownlocations',$this->Location->findAll(array($locationgenre => '1'),'','locationname ASC'));
		}
		
		if ($smlhood)
		{
		$hood = $smlhood;
		if ($smlhood == "CoopCity"){$hood = "Coop City";}
		if ($smlhood == "HighBridge"){$hood = "High Bridge";}
		if ($smlhood == "MorrisHeights"){$hood = "Morris Heights";}
		if ($smlhood == "MottHaven"){$hood = "Mott Haven";}
		if ($smlhood == "OtherBronx"){$hood = "Other Bronx";}
		if ($smlhood == "BedfordStuyvesant"){$hood = "Bedford-Stuyvesant";}
		if ($smlhood == "ClintonHill"){$hood = "Clinton Hill";}
		if ($smlhood == "CarrollGardensCobbleHill"){$hood = "Carroll Gardens-Cobble Hill";}
		if ($smlhood == "BrooklynHeights"){$hood = "Brooklyn Heights";}
	    
		// if ($smlhood == "CarrollGardens"){$hood = "Carroll Gardens";}
		// if ($smlhood == "CobbleHill"){$hood = "Cobble Hill";}
		if ($smlhood == "ConeyIsland"){$hood = "Coney Island";}
		if ($smlhood == "CrownHeights"){$hood = "Crown Heights";}
		if ($smlhood == "DowntownBrooklyn"){$hood = "Downtown Brooklyn";}
		if ($smlhood == "EastNewYork"){$hood = "East New York";}
		if ($smlhood == "FortGreene"){$hood = "Fort Greene";}
		if ($smlhood == "FortHamilton"){$hood = "Fort Hamilton";}
		if ($smlhood == "OtherBrooklyn"){$hood = "Other Brooklyn";}
		if ($smlhood == "ParkSlope"){$hood = "Park Slope";}
		if ($smlhood == "ProspectHeights"){$hood = "Prospect Heights";}
		if ($smlhood == "RedHook"){$hood = "Red Hook";}
		if ($smlhood == "SunsetPark"){$hood = "Sunset Park";}
		//if ($smlhood == "ColumbiaUniversity"){$hood = "Columbia University";}
		if ($smlhood == "EastHarlem"){$hood = "East Harlem";}
		if ($smlhood == "EastVillage"){$hood = "East Village";}
		if ($smlhood == "FinancialDistrict"){$hood = "Financial District";}
		if ($smlhood == "GreenwichVillage"){$hood = "Greenwich Village";}
		if ($smlhood == "LowerEastSide"){$hood = "Lower East Side";}
		if ($smlhood == "MidtownEast"){$hood = "Midtown East";}
		if ($smlhood == "MidtownWest"){$hood = "Midtown West";}
		if ($smlhood == "MurrayHill"){$hood = "Murray Hill";}
		if ($smlhood == "OtherManhattan"){$hood = "Other Manhattan";}
		if ($smlhood == "UpperEastSide"){$hood = "Upper East Side";}
		if ($smlhood == "UpperWestSide"){$hood = "Upper West Side";}
		if ($smlhood == "WashingtonHeights"){$hood = "Washington Heights";}
		if ($smlhood == "WestVillage"){$hood = "West Village";}
		if ($smlhood == "CollegePoint"){$hood = "College Point";}
		if ($smlhood == "FarRockaway"){$hood = "Far Rockaway";}
		if ($smlhood == "ForestHills"){$hood = "Forest Hills";}
		if ($smlhood == "JacksonHeights"){$hood = "Jackson Heights";}
		if ($smlhood == "KewGardens"){$hood = "Kew Gardens";}
		if ($smlhood == "LongIslandCity"){$hood = "Long Island City";}
		if ($smlhood == "MiddleVillage"){$hood = "Middle Village";}
		if ($smlhood == "OtherQueens"){$hood = "Other Queens";}
		if ($smlhood == "RegoPark"){$hood = "Rego Park";}
		if ($smlhood == "OtherStatenIsland"){$hood = "Other Staten Island";}
		if ($smlhood == "ParkHill"){$hood = "Park Hill";}
		if ($smlhood == "SaintGeorge"){$hood = "Saint George";}
		if ($smlhood == "SouthShore"){$hood = "South Shore";}	
				$this->set('hood',$hood);
		$criteria=array('Location.hood' => $hood);
		list($order,$limit,$page) = $this->Pagination->init($criteria); 
		
		$data = $this->Location->findAll($criteria, '', 'locationname ASC', $limit, $page);
		$this->set('data',$data); 		
		//$this->set('knownlocations',$this->Location->findAll(array('Location.hood' => $hood),'','locationname ASC'));
		}
		if ($smltype)
		{
		if ($smltype == "Sid"){$type = "Sidewalk";}
		if ($smltype == "Gar"){$type = "Garden";}
		if ($smltype == "Par"){$type = "Park";}
		if ($smltype == "Pri"){$type = "Privately Controlled Public Space";}
		if ($smltype == "Blo"){$type = "Block Party or Street Fair";}
		if ($smltype == "Oth"){$type = "Other";}
		$this->set('type', $type);
		$criteria=array('Location.type' => $type);

		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Location->findAll($criteria, '', 'locationname ASC', $limit, $page);
		$this->set('data',$data); 
		//$this->set('knownlocations',$this->Location->findAll(array('Location.type' => $type),'','locationname ASC'));
		}
	}
function view($id = null)
	{ 
		$this->checkSession();
		
		$nowuser = $this->Session->read('user');
		$this->pageTitle = 'Location Information';
		$this->set('booked',false);
		$this->set('requested',false);
		if (!$id)
	  		{ 
   				$this->Session->setFlash('<div class="error">Invalid Location.</div><br>'); 
   				$this->redirect('/locations');  
  			} 
  			$res_location = $this->Location->read(null, $id);
  			$thisuser = $this->User->findByEmailaddress($nowuser);
			$res_user = $this->User->findById($res_location['Location']['user_id']);
			$res_artist = $this->Artist->findByUser_id($thisuser['User']['id']);			
			$this->set('Location', $res_location['Location']);
			$this->set('User', $res_user);
			$this->set('nowuser', $thisuser);
			$this->set('confirmed_locations',$this->Performance->findAll(array('Performance.artist_confirmed' => '1','Performance.location_confirmed' => '1', 'Performance.admin_confirmed' => '1', 'Performance.location_id' => $res_location['Location']['id'])));
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
		
		$this->pageTitle = 'Request this Location';
		$nowuser = $this->Session->read('user');
		$res_user = $this->User->findByEmailaddress($nowuser);
		$res_artist = $this->Artist->findByUser_id($res_user['User']['id']);
		$book_location = $this->Location->findById($id);
		$book_user = $this->User->findById($book_location['Location']['user_id']);
		$this->set('Artist',$res_artist['Artist']);
		$this->set('Location', $book_location['Location']);
		$this->set('User',$book_user['User']);
		$email = $book_user['User']['emailaddress'];
		$fname = $book_user['User']['first_name'];
		$lname = $book_user['User']['last_name'];
		$fullname = $fname.' '.$lname;
		if (!$book_location)
		{
   			//$this->Session->setFlash('Invalid Artist2'); 
  			$this->redirect('/users'); 			
		}
		if (!$res_artist)
		{
			$this->redirect('/users');
		}
		if (!$id && empty($this->data))
  		{ 
   			$this->Session->setFlash('<div class="error">Invalid Location.</div><br>'); 
  			$this->redirect('/locations'); 
 		} 
 		if ($res_user['User']['user_type'] == '180')
		{
			$this->Session->setFlash('<div class="error">You are a Location. You must book an Artist</div><br>'); 
  			$this->redirect('/artists');
		}
		if (!empty($this->data))
 		{
			$this->data['Performance']['artist_id'] = $res_artist['Artist']['id'];
			$this->data['Performance']['artist_confirmed'] = '1';
			$this->data['Performance']['start_time'] = $this->data['Performance']['start_hour'].":".$this->data['Performance']['start_min']." ".$this->data['Performance']['start_ampm'];
			$this->data['Performance']['end_time'] = $this->data['Performance']['end_hour'].":".$this->data['Performance']['end_min']." ".$this->data['Performance']['end_ampm'];
			if ($book_location['Location']['type'] == 'Park')
			{
				$this->data['Performance']['location_confirmed'] = '0';
			}
            $type = 'Artist';
			$genres = array('blues', 'cabaret', 'classical', 'country', 'electronic', 'experimental', 'folk', 'hiphop', 'jazz', 'kids', 'latin', 'opera', 'pop', 'reggae', 'religious', 'rock', 'soul', 'standards', 'world', 'other');
			$genre = "";
			foreach ($genres as $keyword) {
				if ($res_artist[$type][$keyword] == '1')
				{
					$genre .= "".ucwords($keyword);
				}
				if ($genre === "") {
					$genre = 'No Genre(s)';
				}
			}
/* 
   		if ($res_artist['Artist']['blues'] == '1')
		{
		$genre = 'Blues';
 		if ($res_artist['Artist']['cabaret'] == '1'){$genre = $genre. '/Cabaret';} 			
  		if ($res_artist['Artist']['classical'] == '1'){$genre = $genre. '/Classical';}			
 		if ($res_artist['Artist']['country'] == '1'){$genre = $genre. '/Country';	} 			
  		if ($res_artist['Artist']['electronic'] == '1'){$genre = $genre. '/Electronic';} 
 		if ($res_artist['Artist']['experimental'] == '1'){$genre = $genre. '/Experimental';} 			
  		if ($res_artist['Artist']['folk'] == '1'){$genre = $genre. '/Folk';} 			
 		if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 		if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 		if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  		if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 		if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  		if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 		if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  		if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 		if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  		if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  		if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 		if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  		if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';}  				 	 			}
 	if ($res_artist['Artist']['blues'] == '0' &&
 		 	$res_artist['Artist']['cabaret'] == '1')	
 		 	{$genre = 'Cabaret';
 		 	if ($res_artist['Artist']['classical'] == '1'){$genre = $genre. '/Classical';}		
 			if ($res_artist['Artist']['country'] == '1'){$genre = $genre. '/Country';	} 			
  			if ($res_artist['Artist']['electronic'] == '1'){$genre = $genre. '/Electronic';} 
 			if ($res_artist['Artist']['experimental'] == '1'){$genre = $genre. '/Experimental';} 			
  			if ($res_artist['Artist']['folk'] == '1'){$genre = $genre. '/Folk';} 			
 			if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '1')
 			{$genre = 'Classical';
 			if ($res_artist['Artist']['country'] == '1'){$genre = $genre. '/Country';	} 			
  			if ($res_artist['Artist']['electronic'] == '1'){$genre = $genre. '/Electronic';} 
 			if ($res_artist['Artist']['experimental'] == '1'){$genre = $genre. '/Experimental';} 			
  			if ($res_artist['Artist']['folk'] == '1'){$genre = $genre. '/Folk';} 			
 			if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '1')
			{$genre = 'Country';
  			if ($res_artist['Artist']['electronic'] == '1'){$genre = $genre. '/Electronic';} 
 			if ($res_artist['Artist']['experimental'] == '1'){$genre = $genre. '/Experimental';} 			
  			if ($res_artist['Artist']['folk'] == '1'){$genre = $genre. '/Folk';} 			
 			if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		 
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '0' &&
 			$res_artist['Artist']['electronic'] == '1')
			{$genre = 'Electronic';
 			if ($res_artist['Artist']['experimental'] == '1'){$genre = $genre. '/Experimental';} 			
  			if ($res_artist['Artist']['folk'] == '1'){$genre = $genre. '/Folk';} 			
 			if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		 
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '0' &&
 			$res_artist['Artist']['electronic'] == '0' &&
 			$res_artist['Artist']['experimental'] == '1')
			{$genre = 'Experimental';
  			if ($res_artist['Artist']['folk'] == '1'){$genre = $genre. '/Folk';} 			
 			if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '0' &&
 			$res_artist['Artist']['electronic'] == '0' &&
 			$res_artist['Artist']['experimental'] == '0' &&
 			$res_artist['Artist']['folk'] == '1')
			{$genre = 'Folk';
 			if ($res_artist['Artist']['hiphop'] == '1'){$genre = $genre. '/Hip-Hop';}
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '0' &&
 			$res_artist['Artist']['electronic'] == '0' &&
 			$res_artist['Artist']['experimental'] == '0' &&
 			$res_artist['Artist']['folk'] == '0' &&
 			$res_artist['Artist']['hiphop'] == '1')
			{$genre = 'Hip-Hop';
 			if ($res_artist['Artist']['jazz'] == '1'){$genre = $genre. '/Jazz';}
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '0' &&
 			$res_artist['Artist']['electronic'] == '0' &&
 			$res_artist['Artist']['experimental'] == '0' &&
 			$res_artist['Artist']['folk'] == '0' &&
 			$res_artist['Artist']['hiphop'] == '0' &&
 			$res_artist['Artist']['jazz'] == '1')
			{$genre = 'Jazz';
 			if ($res_artist['Artist']['kids'] == '1'){$genre = $genre. '/Kids';} 			
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 			$res_artist['Artist']['cabaret'] == '0' &&
 			$res_artist['Artist']['classical'] == '0' &&
 			$res_artist['Artist']['country'] == '0' &&
 			$res_artist['Artist']['electronic'] == '0' &&
 			$res_artist['Artist']['experimental'] == '0' &&
 			$res_artist['Artist']['folk'] == '0' &&
 			$res_artist['Artist']['hiphop'] == '0' &&
 			$res_artist['Artist']['jazz'] == '0' &&
 			$res_artist['Artist']['kids'] == '1')
			{$genre = 'Kids';
  			if ($res_artist['Artist']['latin'] == '1'){$genre = $genre. '/Latin';} 			
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		 $res_artist['Artist']['experimental'] == '0' &&
 		 $res_artist['Artist']['folk'] == '0' &&
 		 $res_artist['Artist']['hiphop'] == '0' &&
 		 $res_artist['Artist']['jazz'] == '0' &&
 		 $res_artist['Artist']['kids'] == '0' &&
 		 $res_artist['Artist']['latin'] == '1')
			{$genre = 'Latin';
 			if ($res_artist['Artist']['opera'] == '1'){$genre = $genre. '/Opera';} 			
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '1')
			{$genre = 'Opera';
  			if ($res_artist['Artist']['pop'] == '1'){$genre = $genre. '/Pop';} 
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '1')
			{$genre = 'Pop';
 			if ($res_artist['Artist']['reggae'] == '1'){$genre = $genre. '/Reggae';} 			
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	} 		  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '1')
			{$genre = 'Reggae';
  			if ($res_artist['Artist']['religious'] == '1'){$genre = $genre. '/Religious';} 			
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}	  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '1')
			{$genre = 'Religious';
 			if ($res_artist['Artist']['rock'] == '1'){$genre = $genre. '/Rock';}			
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}	  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '0' &&
 		$res_artist['Artist']['rock'] == '1')
			{$genre = 'Rock';
  			if ($res_artist['Artist']['soul'] == '1'){$genre = $genre. '/Soul';}
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}	  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '0' &&
 		$res_artist['Artist']['rock'] == '0' &&
 		$res_artist['Artist']['soul'] == '1')
			{$genre = 'Soul';
  			if ($res_artist['Artist']['standards'] == '1'){$genre = $genre. '/Standards';} 			
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}	  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '0' &&
 		$res_artist['Artist']['rock'] == '0' &&
 		$res_artist['Artist']['soul'] == '0' &&
 		$res_artist['Artist']['standards'] == '1')
			{$genre = 'Standards';
 			if ($res_artist['Artist']['world'] == '1'){$genre = $genre. '/World';} 			
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}	  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '0' &&
 		$res_artist['Artist']['rock'] == '0' &&
 		$res_artist['Artist']['soul'] == '0' &&
 		$res_artist['Artist']['standards'] == '0' &&
 		$res_artist['Artist']['world'] == '1')
			{$genre = 'World';
  			if ($res_artist['Artist']['other'] == '1'){$genre = $genre. '/Other';} 
 		 	}	  		 	
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '0' &&
 		$res_artist['Artist']['rock'] == '0' &&
 		$res_artist['Artist']['soul'] == '0' &&
 		$res_artist['Artist']['standards'] == '0' &&
 		$res_artist['Artist']['world'] == '0' &&
 		$res_artist['Artist']['other'] == '1')
 			{$genre = 'Other';}
 		if ($res_artist['Artist']['blues'] == '0' &&
 		$res_artist['Artist']['cabaret'] == '0' &&
 		$res_artist['Artist']['classical'] == '0' &&
 		$res_artist['Artist']['country'] == '0' &&
 		$res_artist['Artist']['electronic'] == '0' &&
 		$res_artist['Artist']['experimental'] == '0' &&
 		$res_artist['Artist']['folk'] == '0' &&
 		$res_artist['Artist']['hiphop'] == '0' &&
 		$res_artist['Artist']['jazz'] == '0' &&
 		$res_artist['Artist']['kids'] == '0' &&
 		$res_artist['Artist']['latin'] == '0' &&
 		$res_artist['Artist']['opera'] == '0' &&
 		$res_artist['Artist']['pop'] == '0' &&
 		$res_artist['Artist']['reggae'] == '0' &&
 		$res_artist['Artist']['religious'] == '0' &&
 		$res_artist['Artist']['rock'] == '0' &&
 		$res_artist['Artist']['soul'] == '0' &&
 		$res_artist['Artist']['standards'] == '0' &&
 		$res_artist['Artist']['world'] == '0' &&
 		$res_artist['Artist']['other'] == '0')
 			{$genre = 'No Genre(s)';}     
 */
			if ($this->Performance->save($this->data,false))
			{
				$this->Mailer->Subject = "You've received a Make Music New York request!";
   				$this->Mailer->Message = 
   				"An artist has requested to perform at your location for Make Music New York!\n\n".
   				
   				"Artist:  ".$res_artist['Artist']['groupname'].
				"\nGenre(s):  ".str_replace("/", " ", $genre).	
   				"\nTime Requested:  ".$this->data['Performance']['start_time']." - ".$this->data['Performance']['end_time'].
				"\nLocation Requested:  ".$book_location['Location']['locationname'].
   				"\nNotes from Artist:  ".$this->data['Performance']['artist_notes'].
   				
   				"\n\nPlease login to approve, decline, or modify this request:\nhttp://www.".APP_DOMAIN_LOCATION."/".
				$this->emailClose;
				
   				$this->Mailer->AddAddress($fullname , $email);
    			$this->Mailer->socketmail();
				$this->Session->setFlash('<div class="success">Thank you!<br>An e-mail has been sent to '.$book_location['Location']['locationname'].' informing them of your request.<br>You will be notified once they approve or deny it.<br>Please note that final approval from MMNY is required for all performances.</div>'); 
				$this->redirect('/users/pending');
			}
 	    }
 	}

 	function search($search = null)
	{
		$this->checkSession();
		
		$search_term = $this->data['Location']['search'];
//		$this->set('data',NULL);
		$this->set('searchterm', $search_term);		
       	$conditions =array();
        $criteria = array("or" => array('Location.locationname' => 'LIKE %'.$search_term.'%', 'Location.street_add1' => 'LIKE %'.$search_term.'%', 'Location.street_add2' => 'LIKE %'.$search_term.'%', 'Location.city' => 'LIKE %'.$search_term.'%', 'Location.zip_code' => 'LIKE %'.$search_term.'%', 'Location.hood' => 'LIKE %'.$search_term.'%', 'Location.type' => 'LIKE %'.$search_term.'%'));
		$this->Pagination->show = 20;  
		list($order,$limit,$page) = $this->Pagination->init($criteria);
		$data = $this->Location->findAll($criteria, '', 'locationname ASC', $limit, $page);
	   // print_r($data);
		$this->set('data',$data);
	}
}
?>