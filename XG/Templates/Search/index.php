<?php

$lastMentionedArray = array(1 => 'Minutes', 60 => 'Hours', 1440 => 'Days', 10080 => 'Weeks');
$sizeArray1 = array(1 => 'Bigger', 2 => 'Smaller');
$sizeArray2 = array(1 => 'KB', 1024 => 'MB', 1048576 => 'GB');
$botStateArray = array(1 => 'has free Slots', 2 => 'has free Queue', 3 => 'is online');

function arrayToSelect($name, $array)
{
	echo "<select id=".$name.">";
	foreach($array as $key => $value)
	{
		echo "<option value=".$key.">".$value."</option>";
	}
	echo "</select>";
}

?>

<div class="ui-widget-content box">

	<div>
		<label for="searchInput"><img class="icon left" src="images/Search.png"/> Search for: </label>
		<input type="text" name="search" id="searchInput" class="input" value="" size="96" tabindex="10"/>
		<button id="searchOptionsButton">Options</button>
	</div>

	<div id="searchOptions" class="hidden">
		Last Mentioned:
		<input id="lastMentionedValue">
		<?php
		arrayToSelect('lastMentionedSelect', $lastMentionedArray);
		?>
		ago

		Size:
		<?php
		arrayToSelect('sizeSelect1', $sizeArray1);
		?>
		than <input id="sizeValue">
		<?php
		arrayToSelect('sizeSelect2', $sizeArray2);
		?>

		Bot:
		<?php
		arrayToSelect('botState', $botStateArray);
		?>

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