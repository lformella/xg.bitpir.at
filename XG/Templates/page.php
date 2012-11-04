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

<!DOCTYPE html>
<html>

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

		<title>XG.BITPiR.AT<?php echo $title != "" ? " - $title" : ""; ?></title>

		<link rel="stylesheet" type="text/css" media="screen" href="css/reset.css?ver=1.0"/>
		<link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css?ver=1.8.5"/>
		<link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css?ver=4.1.1"/>
		<link rel="stylesheet" type="text/css" media="screen" href="css/ui.multiselect.css?ver=4.1.1"/>
		<link rel="stylesheet" type="text/css" media="screen" href="css/fontello.css?ver=1.0"/>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css?ver=1.2"/>

		<script src="js/json2.min.js" type="text/javascript"></script>

		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="js/jquery-ui.min.js" type="text/javascript"></script>

		<script src="js/i18n/grid.locale-en.js" type="text/javascript"></script>
		<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>

		<script src="js/jquery.sudoSlider.min.js" type="text/javascript"></script>

		<script src="js/jquery.delayedObserver.js" type="text/javascript"></script>

		<script src="js/jquery.class.js" type="text/javascript"></script>

		<script src="js/i18n/xg.locale-en.js" type="text/javascript"></script>
		<script src="js/xg.enum.js" type="text/javascript"></script>
		<script src="js/xg.helper.js" type="text/javascript"></script>
		<script src="js/xg.formatter.js" type="text/javascript"></script>
		<script src="js/xg.js" type="text/javascript"></script>

	</head>

	<body>

		<a href="https://github.com/lformella/xg.bitpir.at">
			<img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png" alt="Fork me on GitHub">
		</a>

		<div id="headerBox" class="box">

			<div id="headerColor" class="box"></div>
			<img id="headerImage" src="images/xg.png">

			<div id="searchBox">
				<input id="searchInput2" class="searchInput"/>
				<i class="icon-big icon-search"></i>
			</div>

			<div id="menuBox">
				<a<?php echo $view == "index" ? " class=\"active\"" : ""; ?> href="?show=index">
					<i class="icon-big icon-globe"></i> Start
				</a>
				<a<?php echo $view == "network" ? " class=\"active\"" : ""; ?> href="?show=network">
					<i class="icon-big icon-book"></i> Networks
				</a>
				<a<?php echo $view == "search" ? " class=\"active\"" : ""; ?> href="?show=search">
					<i class="icon-big icon-search"></i> Search
				</a>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZW786UWCWEJWL" id="link-donate" target="_blank">
					<i class="icon-big icon-money"></i> Donate
				</a>
			</div>

			<div class="clear"></div>
		</div>

		<div class="clear"></div>

		<?php echo $content; ?>

		<div class="ui-widget-content box">Powered by: <a href="https://github.com/lformella/xdcc-grabscher" target="_blank">XG v1.0.0</a>
			<?php
			$pid = @file_get_contents("/home/lars/xg/pid");

			if ($pid != "")
			{
				exec("LANG=en_US.UTF-8 ps -p " . $pid . "| grep -v PID | cut -d&quot; &quot; -f2", $data);
				if (sizeof($data) > 1)
				{
					echo " <i class=\"icon-ok-circle2 ChameleonDark\"></i> ";
				}
			}
			?>
			using XG.Server.Backend.MySql
		</div>

		<!-- Piwik -->
		<script type="text/javascript">
			var pkBaseURL = (("https:" == document.location.protocol) ? "https://52g.de/piwik/" : "http://52g.de/piwik/");
			document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			try
			{
				var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 5);
				piwikTracker.trackPageView();
				piwikTracker.enableLinkTracking();
			}
			catch (err)
			{
			}
		</script>
		<noscript><p><img src="http://52g.de/piwik/piwik.php?idsite=5" style="border:0" alt=""/></p></noscript>
		<!-- End Piwik Tracking Code -->

	</body>
</html>
