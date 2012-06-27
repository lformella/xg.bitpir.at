<?php

$lastMentionedArray = array(60 => 'Minutes', 3600 => 'Hours', 86400 => 'Days', 604800 => 'Weeks');
$sizeArray = array(1024 => 'KB', 1048576 => 'MB', 1073741824 => 'GB');
$botStateArray = array(0 => 'ignore', 1 => 'has free Slots', 2 => 'has free Queue', 3 => 'is online');

function arrayToSelect($name, $array)
{
	echo "<select id='".$name."' class='triggerSearch'>";
	foreach($array as $key => $value)
	{
		echo "<option value='".$key."'>".$value."</option>";
	}
	echo "</select>";
}

?>

<div class="ui-widget-content box">

	<div>
		<label for="searchInput"><img class="icon left" src="images/Search.png"/> Search for: </label>
		<input type="text" name="search" id="searchInput" class="input searchInput" value="" size="96" tabindex="10"/>
		<button id="searchOptionsButton">Options</button>
	</div>

	<div id="searchOptions" class="hidden">
		<div class="left">
			Last Mentioned:
			<input id="lastMentionedValue" class="triggerSearch">
			<?php
			arrayToSelect('lastMentionedSelect', $lastMentionedArray);
			?>
			ago
		</div>

		<div class="left">
			Size: bigger than
			<input id="sizeMinValue" class="triggerSearch">
			<?php
			arrayToSelect('sizeMinSelect', $sizeArray);
			?>
			smaller than
			<input id="sizeMaxValue" class="triggerSearch">
			<?php
			arrayToSelect('sizeMaxSelect', $sizeArray);
			?>
		</div>

		<div class="left">
			Bot:
			<?php
			arrayToSelect('botState', $botStateArray);
			?>
		</div>

		<div class="clear"></div>
	</div>

	<table id="search"></table>
	<div id="search-pager"></div>

	<div id="ircLink">&nbsp;</div>

</div>

<script type="text/javascript">
	$(function ()
	{
		new SearchController();
	});
</script>