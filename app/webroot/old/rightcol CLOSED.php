<hr class="space">

<!-- REGISTRATION CLOSED -->

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

<!-- REGISTRATION CLOSED -->

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