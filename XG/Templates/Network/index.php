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

	<div id="breadCrumb" class="ui-widget-header">
		<ul>
			<li id="bread-home" class="button icon-right-open"><i class="icon-globe"></i> Home</li>
			<li id="bread-server" class="button hidden icon-right-open"><i class="icon-book"></i> <label id="bread-server-name" class="button"></label></li>
			<li id="bread-channel" class="button hidden icon-right-open"><i class="icon-folder"></i> <label id="bread-channel-name" class="button"></label></li>
			<li id="bread-bot" class="button hidden icon-right-open"><i class="icon-user"></i> <label id="bread-bot-name" class="button"></label></li>
		</ul>
	</div>

	<div id="network-slider">
		<ul>
			<li id="slider-servers">
				<table id="servers"></table>
				<div id="servers-pager"></div>
			</li>
			<li id="slider-channels">
				<table id="channels"></table>
				<div id="channels-pager"></div>
			</li>
			<li id="slider-bots">
				<table id="bots"></table>
				<div id="bots-pager"></div>
			</li>
			<li id="slider-packets">
				<table id="packets"></table>
				<div id="packets-pager"></div>
			</li>
		</ul>
	</div>

	<div id="ircLink">&nbsp;</div>

</div>

<script type="text/javascript">
	$(function ()
	{
		new NetworkController();
	});
</script>
