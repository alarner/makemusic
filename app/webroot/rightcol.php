	</div>
</div>

<div id="sidebar-right" class="static">
	<div id="mailing-list">
		<div id="load_check"></div>
		<script type="text/javascript">signupFormObj.drawForm();</script>
	</div>                
	<?php if (isset($session->view->_viewVars['User']['id'])) {	?>
		<div id="admin-menu">
			<table>
				<tr>
					<th class="altRow"><h3>Admin</h3></td>
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
	     <?php } ?>
		<hr class="space">
		<?php if (date("m/d/y") < date("04/25/2012")) { ?> 
	  	<?php if (!isset($session->view->_viewVars['User']['id'])) { ?>
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
					<td>
			Join us for the sixth annual Make Music New York! Through our growing listings of musicians and locations, you will make the connections to collaborate on an outdoor concert. <br />Registration closes on April 21st. <br /><a href='http://makemusicny.org/participate/users/register'>Sign up</a> now to take part!
					</td>
				</tr>
			</tbody>
		</table>
		<?php } ?>
		<?php } else { ?>
<hr class="space">  
<?php //print_r($this); ?>
<!-- <table>
	<thead>
		<tr>
			<th class="altRow">
				<h3>Sign up for MMNY</h3>
			</th>
		</tr>
	<tbody>
		<tr>
			<td>
	Join us for the sixth annual Make Music New York! Through our growing listings of musicians and locations, you will make the connections to collaborate on an outdoor concert. Registration closes on April 21st. <a href='http://makemusicny.org/participate/participate'>Sign up</a> now to take part!
			</td>
		</tr>
	</tbody>
</table>

REGISTRATION CLOSED 
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
REGISTRATION CLOSED -->
<hr class="space">
<table>
	<thead>
		<tr>
			<th class="altRow">
				<h3>Performances</h3>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>


				<?php if ($confirmed_locations || $confirmed_artists){echo nl2br("View Performances you have Confirmed\n".$html->link('View your Confirmed Performances', '/users/confirmed/'))."<hr class='space'>";} ?>
				Search for Performances in your area<br><a href="<?php echo $this->webroot;?>performances/">Find Performances</a>
			</td>
		</tr>
	</tbody>
</table>
<hr class="space">
<table>
	<thead>
		<tr>
			<th class="altRow">
				<h3>More Information</h3>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<a href="http://www.makemusicny.org/" >Visit the MMNY official site</a><hr class="space">
				<a href="/newyork/section/music" >Find out about this week's top live shows</a>
			</td>
		</tr>
	</tbody>
</table>
<?php } ?>
