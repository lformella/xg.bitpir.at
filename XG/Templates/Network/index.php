<div class="ui-widget-content form-box">

	<div class="ui-widget-header breadCrumb">
		<ul>
			<li id="bread-home" class="button"><img src="images/client.png"/> Home</li>
			<li id="bread-server" class="button hidden"></li>
			<li id="bread-channel" class="button hidden"></li>
			<li id="bread-bot" class="button hidden"></li>
		</ul>
	</div>

	<div id="network-slider">
		<ul>
			<li>
				<table id="servers"></table>
				<div id="servers-pager"></div>
			</li>
			<li>
				<table id="channels"></table>
				<div id="channels-pager"></div>
			</li>
			<li>
				<table id="bots"></table>
				<div id="bots-pager"></div>
			</li>
			<li>
				<table id="packets"></table>
				<div id="packets-pager"></div>
			</li>
		</ul>
	</div>

	<div class="footer" id="current_object">&nbsp;</div>

</div>

<script type="text/javascript">
	$(function ()
	{
		new NetworkController();
	});
</script>
