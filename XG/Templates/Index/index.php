<?php
//
//  index.php
//
//  Author:
//       Lars Formella <ich@larsformella.de>
//
//  Copyright (c) 2012 Lars Formella
//
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the Free Software
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//
?>

<div class="ui-widget-content box">

	<img src="images/Server.png" alt=""/> <?php echo $count[0] ?> Server connected<br/>
	<img src="images/Server_disabled.png" alt=""/> <?php echo $count[1] ?> Server disconnected<br/><br/>
	<img src="images/Channel.png" alt=""/> <?php echo $count[2] ?> Channels<br/>
	<img src="images/Bot.png" alt=""/> <?php echo $count[3] ?> Bots<br/>
	<img src="images/Packet.png" alt=""/> <?php echo $count[4] ?> Packets<br/>

	<div id="lastSearches">
		<div>Last searches:</div>
		<?php
		foreach($searches as $search => $count)
		{
			echo '<span>|</span> ';
			echo '<span><a href="?show=search#' . $search . '" title="Searched ' . $count . ' time(s)">' . str_replace(' ', '&nbsp;', $search) . '</a></span>';
		}
		echo '<span>|</span> ';
		?>
	</div>

</div>

<script type="text/javascript">
	$(function ()
	{
		new IndexController();
	});
</script>
