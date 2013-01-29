			</div>
		</div>
		<div id="sidebar-right" class="static">
			<div id="mailing-list">
				<div id="load_check"></div>
				<form method="post" id="e2ma_signup" onsubmit="return signupFormObj.checkForm(this)" action="https://app.e2ma.net/app/view:Join/acctId:1407539/signupId:1415763">
					<fieldset>
						<input type="hidden" name="private_set" value="2" />
						<input type="hidden" name="groups[]" value="208172666" />
						<input type="hidden" name="groups[]" value="208172670" />
						<div class="e2ma_signup_form_row">
							<div class="e2ma_signup_form_element"><input name="emma_member_email" type="text" /></div>
						</div>
						<div class="e2ma_signup_form_button_row" id="e2ma_signup_form_button_row">
							<input id="e2ma_signup_submit_button" class="e2ma_signup_form_button" type="submit" name="Submit" value="Submit" />
						</div>
					</fieldset>
				</form>
			</div>
	<div class="date-box"></div>

    <?php if (date("m/d/y") < FINAL_CLOSE_DATE) { ?>               
	
	<?php  if (!in_array($html->action, array('display', 'login', 'register', 'reset'))) { //if ($session->check('Auth.User.id')) { //if (isset($session->view->_viewVars['User']['id'])) {	?>      
		
	<div id="admin-menu">
		<table>
			<tr>
				<th class="altRow"><h3>Menu</h3></td>
			</tr>
			<tr>
				<td>
					<ul>
						<li><a href="<?php echo $this->webroot;?>users/">My Account</a></li>
						<li><a href="<?php echo $this->webroot;?>artists/" class="active">Artists</a></li>
						<li><a href="<?php echo $this->webroot;?>locations/">Locations</a></li>
						<li><a href="<?php echo $this->webroot;?>performances/">Performances</a></li>
						<a href='<?php echo $this->webroot;?>users/logout'>Log Out</a>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	<hr class="space">          
	
	<?php } else { ?>
	<!--
	<table>
		<thead>
			<tr>
				<th class="altRow">
					<h3>Log In</h3>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<strong>Taking part in Make Music New York?<br><a href="<?php echo $this->webroot;?>users/login/">Log in to Make Music</a></strong>
				</td>
			</tr>
		</tbody>
	</table>
	<hr class="space">
	<table>
		<thead>
			<tr>
				<th class="altRow">
					<h3>Sign up for MMNY</h3>
				</th>
			</tr>
		<tbody>
			<tr>
				<td>Join us for the sixth annual Make Music New York! Through our growing listings of musicians and locations, you will make the connections to collaborate on an outdoor concert. <br />Registration closes on April 21st. <br /><a href='http://makemusicny.org/participate/users/register'>Sign up</a> now to take part!</td>
			</tr>
		</tbody>
	</table>
	-->
	<?php } ?>   
	
	<?php } else { ?>
	
	<table>
		<thead>
			<tr>
				<th class="altRow">
					<h3>Matchmaking Closed</h3>
				</th>
			</tr>
		<tbody>
			<tr>
				<td>
	MMNY's "matchmaking" system is now closed for 2012.<hr class="space">
	If you are already confirmed for a MMNY performance and need to change any details, please email <a href="mailto:mmny.corrections@gmail.com">mmny.corrections@gmail.com</a>.<hr class="space">
	For all other questions, email <a href="mailto:makemusicny@gmail.com">makemusicny@gmail.com</a>.
				</td>
			</tr>
		</tbody>
	</table>
	<?php } ?>
	<!--
	<hr class="space">
	<table>
		<thead>
			<tr>
				<th class="altRow">
					<h3>Video: Make Music New York</h3>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<a href="http://newyork.timeout.com/section/video" title="see more videos" target="new">See more videos</a>
					<object height="225" width="300" codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab" id="ooyalaPlayer_92pb4_fs4z9bap" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param value="http://www.ooyala.com/player.swf" name="movie"/><param value="high" name="quality"/><param value="#000000" name="bgcolor"/><param value="always" name="allowScriptAccess"/><param value="true" name="allowFullScreen"/><param value="embedCode=NzNG1jOmYeFNat2Ebc-Qryc_HhUrXjFn" name="flashvars"/><embed height="225" width="300" align="middle" pluginspage="http://www.adobe.com/go/getflashplayer" flashvars="embedCode=NzNG1jOmYeFNat2Ebc-Qryc_HhUrXjFn" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" quality="high" loop="false" play="true" name="ooyalaPlayer_92pb4_fs4z9bap" bgcolor="#000000" src="http://www.ooyala.com/player.swf"/></object>
				</td>
			</tr>
		</tbody>
	</table>
	-->
			<div id="social-links">
				<ul>
					<li id="social-youtube"><a  class="sprite external" href="http://youtube.com/makemusicny" title="Watch our Videos on YouTube" target="_blank">&nbsp;</a></li>
					<li id="social-flickr"><a  class="sprite external" href="http://flickr.com/makemusicny" title="See our Photo Archive on Flickr" target="_blank">&nbsp;</a></li>
					<li id="social-facebook"><a  class="sprite external" href="http://facebook.com/makemusicny" title="Become a Fan on Facebook" target="_blank">&nbsp;</a></li>
					<li id="social-twitter"><a  class="sprite external" href="http://twitter.com/makemusicny" title="Follow us @makemusicny" target="_blank">&nbsp;</a></li>
				</ul>
			</div>
		</div>
		<div style="clear:both"></div>
	</div>
