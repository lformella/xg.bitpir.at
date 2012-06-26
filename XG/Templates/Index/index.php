<div class="ui-widget-content form-box">

	<img src="images/Server.png" alt=""/> <?php echo $count[0] ?> Server connected<br/>
	<img src="images/Server_disabled.png" alt=""/> <?php echo $count[1] ?> Server disconnected<br/><br/>
	<img src="images/Channel.png" alt=""/> <?php echo $count[2] ?> Channels<br/>
	<img src="images/Bot.png" alt=""/> <?php echo $count[3] ?> Bots<br/>
	<img src="images/Packet.png" alt=""/> <?php echo $count[4] ?> Packets<br/>

</div>

<script type="text/javascript">
	$(function ()
	{
		new IndexController();
	});
</script>