<hr class="space">

<?php if ($create){echo nl2br("<table><thead><tr><th class='altRow'><h3>Your ".ucwords($user_type)." Profile</h3></th></tr></thead><tbody><tr><td>You have yet to create your ". ucwords($user_type)." Profile\n".$html->link('Click here to create your '.ucwords($user_type), '/'.$user_type.'s/create'))."</td></tr></tbody></table><hr class='space'>";}  ?>
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
				<?php if ($has_artist){echo nl2br("Search for Locations for your Artist to perform at\n".$html->link('Find Locations', '/locations/')."<hr class='space'>Create your own performance for your artist\n".$html->link('Create your Performance', '/users/ownlocation/')."<hr class='space'>");}  ?>
				<?php if ($has_location){echo nl2br("Search for artists to perform at your location\n".$html->link('Find Artists', '/artists/')."<hr class='space'>Create your own performance at your location\n".$html->link('Create your Performance', '/users/ownartist/')."<hr class='space'>");}  ?>
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