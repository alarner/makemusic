<div id="navcontainer">
<ul id="navlist">
<li id="active"><a href="<?php echo $this->webroot;?>users/" <?php if ($html->action == 'index') echo 'id="current"'; ?>>My User</a></li>    
<!-- <?php //print($html->action) ; echo $confirmed_locations."<br />"; echo confirmed_artists."<br />";?> -->
<?php if ($has_artist){
	echo"<li><a href='".$this->webroot."users/artist'"; 
		if ($html->action == 'artist') echo 'id=current'; 
	echo ">My Artist</a></li>";
	}?>
	<?php if ($has_multiple_locations){
			echo"<li><a href='".$this->webroot."users/listall'"; 
			if ($html->action == 'listall') echo 'id=current'; 
		    echo ">My Locations</a></li>";
		}
		?>

<?php if ( $has_location && !$has_multiple_locations){
		echo"<li><a href='".$this->webroot."users/location'"; 
			if ($html->action == 'location') echo 'id=current'; 
	   echo ">My Location</a></li>";
	}?>
<?php if ($confirmed_locations || $confirmed_artists){
	echo"<li><a href='".$this->webroot."users/confirmed'"; 
		if ($html->action == 'confirmed') echo 'id="current"'; 
	echo ">Confirmed Performances</a></li>";
	}?>
<?php if ($pending_locations || $pending_artists || $your_locations || $your_artists){ 
		echo "<li><a href='".$this->webroot."users/pending'" ; 
		if ($html->action == 'pending') { echo 'id="current"'; }
		echo ">Pending Performances"; 
		if ($your_locations){
			echo " (".count($your_locations).")";
		} if ($your_artists){
			echo " (".count($your_artists).")";
		} ?></a></li>
   <? }?>
<?php if ($User['user_type'] == 160 && TODAY < CLOSE_DATE){
		echo"<li><a href='".$this->webroot."users/ownlocation'>Own Location</a></li>";
	   }?>
<?php if ($User['user_type'] == 180 && TODAY < CLOSE_DATE){echo"<li><a href='".$this->webroot."users/ownartist'>Own Artist</a></li>";
	   }?>
</ul>
</div>
