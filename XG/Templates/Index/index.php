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

/** @var $snapshot XG\Classes\Domain\Model\Snapshot */

function SizeToHuman($size)
{
	if ($size == 0)
	{
		return "&nbsp;";
	}
	if ($size < 1024)
	{
		return $size . " B";
	}
	else if ($size < 1024 * 1024)
	{
		return round($size / 1024) . " KB";
	}
	else if ($size < 1024 * 1024 * 1024)
	{
		return round($size / (1024 * 1024)) . " MB";
	}
	else if ($size < 1024 * 1024 * 1024 * 1024)
	{
		return round($size / (1024 * 1024 * 1024)) . " GB";
	}
	return round($size / (1024 * 1024 * 1024 * 1024), ($size < 1024 * 1024 * 1024 * 1024 * 10) ? 1 : 0) . " TB";
}
?>

<div class="ui-widget-content box" id="status">

	<p>
		Live searching in:
	</p>
	<p class="main">
		<i class="icon-big icon-book"></i> <?php echo $snapshot->Servers; ?> Servers
	</p>
	<p class="data">
		<i class="icon-ok-circle2 ChameleonDark" title="Connected"><label><?php echo $snapshot->ServersConnected; ?></label></i>
		<i class="icon-cancel-circle2 ScarletRedMiddle" title="Disconnected"><label><?php echo $snapshot->ServersDisconnected; ?></label></i>
	</p>
	<p class="main">
		<i class="icon-big icon-folder"></i> <?php echo $snapshot->Channels ?> Channels
	</p>
	<p class="data">
		<i class="icon-ok-circle2 ChameleonDark" title="Connected"><label><?php echo $snapshot->ChannelsConnected; ?></label></i>
		<i class="icon-cancel-circle2 ScarletRedMiddle" title="Disconnected"><label><?php echo $snapshot->ChannelsDisconnected; ?></label></i>
	</p>
	<p class="main">
		<i class="icon-big icon-user"></i> <?php echo $snapshot->Bots ?> Bots
	</p>
	<p class="data">
		<i class="icon-ok-circle2 ChameleonDark" title="Connected"><label><?php echo $snapshot->BotsConnected; ?></label></i>
		<i class="icon-cancel-circle2 ScarletRedMiddle" title="Disconnected"><label><?php echo $snapshot->BotsDisconnected; ?></label></i>
	</p>
	<p class="main">
		<i class="icon-big icon-gift"></i> <?php echo $snapshot->Packets ?> Packets
	</p>
	<p class="data">
		<i class="icon-ok-circle2 ChameleonDark" title="Connected"><label><?php echo SizeToHuman($snapshot->PacketsSize); ?></label></i>
	</p>

	<div id="lastSearches">
		<div>Last searches:</div>
		<?php
		foreach($searches as $search => $count)
		{
			echo '<a href="?show=search#' . $search . '" title="Searched ' . $count . ' time(s)"><i class="icon-search"></i>' . str_replace(' ', '&nbsp;', $search) . '</a>';
		}
		?>
	</div>

</div>

<script type="text/javascript">
	$(function ()
	{
		new IndexController();
	});
</script>
