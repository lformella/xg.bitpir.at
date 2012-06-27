//
// Copyright (C) 2012 Lars Formella <ich@larsformella.de>
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 

/**********************************************************************************************************************/
/* GLOBAL VARS / FUNCTIONS                                                                                            */
/**********************************************************************************************************************/

var id_server, id_channel, id_bot, id_packet, id_search, last_search, Formatter,  Helper;

var LANG_MONTH = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
var LANG_WEEKDAY = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

/**********************************************************************************************************************/
/* CONTROLLER                                                                                                         */
/**********************************************************************************************************************/

var BaseController = Class.create(
{
	initialize: function()
	{
		var self = this;

		Formatter = new MyFormatter();
		Helper = new XGHelper();

		$("#searchInput2").keyup(function (e)
		{
			if (e.which == 13)
			{
				if(!jQuery("#search").length)
				{
					window.location = '?show=search#' + $(this).val();
				}
			}
		});
	},

	trackPiwik: function (url, title)
	{
		if (title == undefined)
		{
			title = document.title;
		}
		try
		{
			piwikTracker.setCustomUrl(url);
			piwikTracker.setDocumentTitle(title);
			piwikTracker.trackPageView();
			piwikTracker.enableLinkTracking();
		}
		catch (err)
		{
		}
	}
});

var IndexController = Class.create(BaseController,
{
	initialize: function($super)
	{
		$super();
		var self = this;
	}
});

var NetworkController = Class.create(BaseController,
{
	initialize: function($super)
	{
		$super();
		var self = this;

		/**************************************************************************************************************/
		/* SERVER GRID                                                                                                */
		/**************************************************************************************************************/

		jQuery("#servers").jqGrid(
		{
			url:"index.php?show=network&action=json&do=get_servers",
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', 'Name', 'Channels', 'Bots', 'Packets', '', '', ''],
			colModel:[
				{name:'Connected',		index:'Connected',		formatter: function(c, o, r) { return Formatter.formatServerIcon(r); }, width:26},
				{name:'Name',			index:'Name',			formatter: function(c, o, r) { return Formatter.formatServerName(r); }, fixed:false},
				{name:'ChannelCount',	index:'ChannelCount',	formatter: function(c, o, r) { return r.ChannelCount; }, width:60, align:"right"},
				{name:'BotCount',		index:'BotCount',		formatter: function(c, o, r) { return r.BotCount; }, width:60, align:"right"},
				{name:'PacketCount',	index:'PacketCount',	formatter: function(c, o, r) { return r.PacketCount; }, width:60, align:"right"},

				{name:'Guid',			index:'Guid',			formatter: function(c, o, r) { return r.Guid; }, hidden: true},
				{name:'ErrorCode',		index:'ErrorCode',		formatter: function(c, o, r) { return r.ErrorCode; }, hidden: true},
				{name:'IrcLink',		index:'IrcLink',		formatter: function(c, o, r) { return r.IrcLink; }, hidden:true}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_server = id;
					var obj = jQuery('#servers').getRowData(id);
					if (obj)
					{
						jQuery("#channels").clearGridData();
						jQuery("#channels").setGridParam({url:"index.php?show=network&action=json&do=get_channels_from_server&guid=" + obj.Guid}).trigger("reloadGrid");
						networkSlider.goToSlide(2);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:jQuery('#servers-pager'),
			sortname:'Name',
			viewrecords:true,
			ExpandColumn:'Name',
			height:'100%',
			autowidth:true,
			sortorder:"asc"
		}).navGrid('#servers-pager', {edit:false, add:false, del:false, search:false});

		/**************************************************************************************************************/
		/* CHANNEL GRID                                                                                               */
		/**************************************************************************************************************/

		jQuery("#channels").jqGrid(
		{
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', 'Name', 'Bots', 'Packets', '', '', ''],
			colModel:[
				{name:'Connected',		index:'Connected',		formatter: function(c, o, r) { return Formatter.formatChannelIcon(r); }, width:26 },
				{name:'Name',			index:'Name',			formatter: function(c, o, r) { return Formatter.formatChannelName(r); }, fixed:false},
				{name:'BotCount',		index:'BotCount',		formatter: function(c, o, r) { return r.BotCount; }, width:60, align:"right"},
				{name:'PacketCount',	index:'PacketCount',	formatter: function(c, o, r) { return r.PacketCount; }, width:60, align:"right"},

				{name:'Guid',			index:'Guid',			formatter: function(c, o, r) { return r.Guid; }, hidden:true},
				{name:'ErrorCode',		index:'ErrorCode',		formatter: function(c, o, r) { return r.ErrorCode; }, hidden:true},
				{name:'IrcLink',		index:'IrcLink',		formatter: function(c, o, r) { return r.IrcLink; }, hidden:true}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_channel = id;
					var obj = jQuery('#channels').getRowData(id);
					if (obj)
					{
						jQuery("#bots").clearGridData();
						jQuery("#bots").setGridParam({url:"index.php?show=network&action=json&do=get_bots_from_channel&guid=" + obj.Guid}).trigger("reloadGrid");
						networkSlider.goToSlide(3);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:jQuery('#channels-pager'),
			sortname:'Name',
			viewrecords:true,
			ExpandColumn:'Name',
			height:'100%',
			autowidth:true,
			sortorder:"asc"
		}).navGrid('#channels-pager', {edit:false, add:false, del:false, search:false});

		/**************************************************************************************************************/
		/* BOT GRID                                                                                                   */
		/**************************************************************************************************************/

		jQuery("#bots").jqGrid(
		{
			//url: "index.php?show=network&action=json&do=get_bots_from_channel&guid=" + guid,
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', 'Name', 'Queue', 'Slots', 'Speed', 'Last Contact', 'Packets', '', '', '', '', '', ''],
			colModel:[
				{name:'Connected',			index:'Connected',			formatter: function(c, o, r) { return Formatter.formatBotIcon(r); }, width:26 },
				{name:'Name',				index:'Name',				formatter: function(c, o, r) { return Formatter.formatBotName(r); }, fixed:false},
				{name:'InfoQueueCurrent',	index:'InfoQueueCurrent',	formatter: function(c, o, r) { return Formatter.formatBotQueue(r); }, width:80, align:"right"},
				{name:'InfoSlotCurrent',	index:'InfoSlotCurrent',	formatter: function(c, o, r) { return Formatter.formatBotSlots(r); }, width:80, align:"right"},
				{name:'InfoSpeedCurrent',	index:'InfoSpeedCurrent',	formatter: function(c, o, r) { return Formatter.formatBotSpeed(r); }, width:120, align:"right"},
				{name:'LastContact',		index:'LastContact',		formatter: function(c, o, r) { return Helper.timeStampToHuman(r.LastContact); }, width:150, align:"right"},
				{name:'PacketCount',		index:'PacketCount',		formatter: function(c, o, r) { return r.PacketCount; }, width:60, align:"right"},

				{name:'InfoSpeedMax',		index:'InfoSpeedMax',		formatter: function(c, o, r) { return r.InfoSpeedMax; }, hidden:true},
				{name:'InfoSlotTotal',		index:'InfoSlotTotal',		formatter: function(c, o, r) { return r.InfoSlotTotal; }, hidden:true},
				{name:'InfoQueueTotal',		index:'InfoQueueTotal',		formatter: function(c, o, r) { return r.InfoQueueTotal; }, hidden:true},
				{name:'BotState',			index:'BotState',			formatter: function(c, o, r) { return r.BotState; }, hidden:true},
				{name:'Guid',				index:'Guid',				formatter: function(c, o, r) { return r.Guid; }, hidden:true},
				{name:'IrcLink',			index:'IrcLink',			formatter: function(c, o, r) { return r.IrcLink; }, hidden:true}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_bot = id;
					var obj = jQuery('#bots').getRowData(id);
					if (obj)
					{
						jQuery("#packets").clearGridData();
						jQuery("#packets").setGridParam({url:"index.php?show=network&action=json&do=get_packets_from_bot&guid=" + obj.Guid}).trigger("reloadGrid");
						networkSlider.goToSlide(4);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:jQuery('#bots-pager'),
			sortname:'Name',
			viewrecords:true,
			ExpandColumn:'Name',
			height:'100%',
			autowidth:true,
			sortorder:"asc"
		}).navGrid('#bots-pager', {edit:false, add:false, del:false, search:false});

		/**************************************************************************************************************/
		/* PACKET GRID                                                                                                */
		/**************************************************************************************************************/

		jQuery("#packets").jqGrid(
		{
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', 'Id', 'Name', 'Last Mentioned', 'Size', '', ''],
			colModel:[
				{name:'Connected',		index:'Connected',		formatter: function(c, o, r) { return Formatter.formatPacketIcon(r); }, width:26},
				{name:'Id',				index:'Id',				formatter: function(c, o, r) { return Formatter.formatPacketId(r); }, width:38, align:"right"},
				{name:'Name',			index:'Name',			formatter: function(c, o, r) { return Formatter.formatPacketName(r); }, fixed:false},
				{name:'LastMentioned',	index:'LastMentioned',	formatter: function(c, o, r) { return Helper.timeStampToHuman(r.LastMentioned); }, width:150, align:"right"},
				{name:'Size',			index:'Size',			formatter: function(c, o, r) { return Helper.size2Human(r.Size); }, width:80, align:"right"},

				{name:'Guid',			index:'Guid',			formatter: function(c, o, r) { return r.Guid; }, hidden:true},
				{name:'IrcLink',		index:'IrcLink',		formatter: function(c, o, r) { return r.IrcLink; }, hidden:true}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_packet = id;
					var obj = jQuery('#packets').getRowData(id);
					if (obj)
					{
						jQuery("#ircLink").html(obj.IrcLink);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:jQuery('#packets-pager'),
			sortname:'Id',
			viewrecords:true,
			ExpandColumn:'Name',
			height:'100%',
			autowidth:true,
			sortorder:"asc"
		}).navGrid('#packets-pager', {edit:false, add:false, del:false, search:true});

		/**************************************************************************************************************/
		/* OTHER STUFF                                                                                                */
		/**************************************************************************************************************/

		var networkSlider = $("#network-slider").sudoSlider({
			controlsShow:false,
			history:true,
			autoheight:false,
			numericText:['servers', 'channels', 'bots', 'packets'],
			beforeAniFunc:function (t)
			{
				var obj = undefined;
				switch (t)
				{
					case 1:
						jQuery("#ircLink").html('&nbsp;');
						jQuery("#bread-server").fadeOut();
						jQuery("#bread-channel").fadeOut();
						jQuery("#bread-bot").fadeOut();
						break;

					case 2:
						obj = jQuery('#servers').getRowData(id_server);
						if (obj)
						{
							jQuery("#ircLink").html(obj.IrcLink);
							jQuery("#bread-server").html(obj.Connected + " " + obj.Name);
							jQuery("#bread-server").fadeIn();
							self.trackPiwik(document.location, document.title + " " + obj.Name);
						}
						jQuery("#bread-channel").fadeOut();
						jQuery("#bread-bot").fadeOut();
						break;

					case 3:
						obj = jQuery('#servers').getRowData(id_server);
						if (obj)
						{
							jQuery("#bread-server").html(obj.Connected + " " + obj.Name);
							jQuery("#bread-server").fadeIn();
						}
						obj = jQuery('#channels').getRowData(id_channel);
						if (obj)
						{
							jQuery("#ircLink").html(obj.IrcLink);
							jQuery("#bread-channel").html(obj.Connected + " " + obj.Name);
							jQuery("#bread-channel").fadeIn();
							self.trackPiwik(document.location, document.title + " " + obj.Name);
						}
						jQuery("#bread-bot").fadeOut();
						break;

					case 4:
						obj = jQuery('#servers').getRowData(id_server);
						if (obj)
						{
							jQuery("#bread-server").html(obj.Connected + " " + obj.Name);
							jQuery("#bread-server").fadeIn();
						}
						obj = jQuery('#channels').getRowData(id_channel);
						if (obj)
						{
							jQuery("#ircLink").html(obj.IrcLink);
							jQuery("#bread-channel").html(obj.Connected + " " + obj.Name);
							jQuery("#bread-channel").fadeIn();
						}
						obj = jQuery('#bots').getRowData(id_bot);
						if (obj)
						{
							jQuery("#bread-bot").html(obj.Connected + " " + obj.Name);
							jQuery("#bread-bot").fadeIn();
							self.trackPiwik(document.location, document.title + " " + obj.Name);
						}
						break;
				}
			}
		});

		$('#bread-home').click(function ()
		{
			networkSlider.goToSlide(1);
		});
		$('#bread-server').click(function ()
		{
			networkSlider.goToSlide(2);
		});
		$('#bread-channel').click(function ()
		{
			networkSlider.goToSlide(3);
		});
		$('#bread-bot').click(function ()
		{
			networkSlider.goToSlide(4);
		});

		$('.button').hover(function ()
		{
			$(this).css('cursor', 'pointer');
		}, function ()
		{
			$(this).css('cursor', 'auto');
		});
	}
});

var SearchController = Class.create(BaseController,
{
	initialize: function($super)
	{
		$super();
		var self = this;

		/**************************************************************************************************************/
		/* SEARCH GRID                                                                                                */
		/**************************************************************************************************************/

		jQuery("#search").jqGrid(
		{
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', 'Id', 'Name', 'Last Mentioned', 'Size', 'Bot', 'Speed', ''],
			colModel:[
				{name:'Connected',		index:'Connected',		formatter: function(c, o, r) { return Formatter.formatPacketIcon(r); }, width:26},
				{name:'Id',				index:'Id',				formatter: function(c, o, r) { return Formatter.formatPacketId(r); }, width:38, align:"right"},
				{name:'Name',			index:'Name',			formatter: function(c, o, r) { return Formatter.formatPacketName(r); }, fixed:false},
				{name:'LastMentioned',	index:'LastMentioned',	formatter: function(c, o, r) { return Helper.timeStampToHuman(r.LastMentioned); }, width:140, align:"right"},
				{name:'Size',			index:'Size',			formatter: function(c, o, r) { return Helper.size2Human(r.Size); }, width:60, align:"right"},
				{name:'BotName',		index:'BotName',		formatter: function(c, o, r) { return Formatter.formatPacketBotName(r.BotName); }, width:80},
				{name:'BotSpeed',		index:'BotSpeed',		formatter: function(c, o, r) { return Helper.speed2Human(r.BotSpeed); }, width:80, align:"right"},

				{name:'IrcLink',		index:'IrcLink',		formatter: function(c, o, r) { return r.IrcLink; }, hidden:true}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_search = id;
					var obj = jQuery('#search').getRowData(id);
					if (obj)
					{
						jQuery("#ircLink").html(obj.IrcLink);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:jQuery('#search-pager'),
			sortname:'Id',
			viewrecords:true,
			ExpandColumn:'Name',
			height:'100%',
			autowidth:true,
			sortorder:"asc"
		}).navGrid('#search-pager', {edit:false, add:false, del:false, search:false});

		/**************************************************************************************************************/
		/* OTHER STUFF                                                                                                */
		/**************************************************************************************************************/

		var search = window.location.hash.substr(1);
		if(search != '')
		{
			window.setTimeout(function()
			{
				self.doSearch(search);
			}, 1000);
		}

		$(".searchInput").keyup(function (e)
		{
			if (e.which == 13)
			{
				self.doSearch($(this).val());
			}
		});

		$("input.triggerSearch").keyup(function (e)
		{
			if (e.which == 13)
			{
				self.doSearch($("#searchInput").val());
			}
		});

		$("select.triggerSearch").change(function (e)
		{
			self.doSearch($("#searchInput").val());
		});

		$('#searchInput').delayedObserver(1.1,
		function (value)
		{
			self.doSearch(value);
		});

		$('#searchOptionsButton').button();
		$('#searchOptionsButton').click(function()
		{
			$('#searchOptions').toggle();
		});
	},

	doSearch: function (value)
	{
		$("#searchInput").val(value);
		$("#searchInput2").val(value);

		var searchUrl = "index.php?show=search&action=json&do=search_packets&searchString=" + value;

		if($('#lastMentionedValue').val() != '')
		{
			searchUrl += "&searchLastMentioned=" + ($('#lastMentionedValue').val() * $('#lastMentionedSelect').val());
		}
		if($('#sizeMin').val() != '')
		{
			searchUrl += "&searchSizeMin=" + ($('#sizeMinValue').val() * $('#sizeMinSelect').val());
		}
		if($('#sizeMax').val() != '')
		{
			searchUrl += "&searchSizeMax=" + ($('#sizeMaxValue').val() * $('#sizeMaxSelect').val());
		}
		if($('#botState').val() != '')
		{
			searchUrl += "&searchBotState=" + $('#botState').val();
		}

		if (last_search != searchUrl)
		{
			last_search = searchUrl;

			jQuery("#search").clearGridData();
			jQuery("#search").setGridParam({url: searchUrl}).trigger("reloadGrid");

			this.trackPiwik(document.location, document.title + " " + value);
		}
	}
});

/**********************************************************************************************************************/
/* CUSTOM FORMATER                                                                                                    */
/**********************************************************************************************************************/

var MyFormatter = Class.create(XGFormatter,
{
	/**********************************************************************************************************************/
	/* SERVER FORMATER                                                                                                    */
	/**********************************************************************************************************************/

	formatIcon: function (img)
	{
		return "<img src='images/" + img + ".png' />";
	},

	formatIcon2: function (img)
	{
		return this.formatIcon(img);
	},

	formatServerIcon: function (server)
	{
		var str = "Server";

		if (server.Connected == 1)
		{
			str += "";
		}
		else
		{
			str += "_disabled";
		}

		return "<a href='" + server.IrcLink + "'>" + this.formatIcon(str) + "</a>";
	},

	formatServerName: function (server)
	{
		var str = server.Name;
		if (server.ErrorCode != "" && server.ErrorCode != null && server.ErrorCode != 0)
		{
			str += " - <small>" + server.ErrorCode + "</small>";
		}
		return str;
	},

	/**********************************************************************************************************************/
	/* CHANNEL FORMATER                                                                                                   */
	/**********************************************************************************************************************/

	formatChannelIcon: function (channel)
	{
		var str = "Channel";

		if(channel.Connected) { str += ""; }
		else if(channel.ErrorCode > 0) { str += "_error"; }
		else { str += "_disabled"; }

		return "<a href='" + channel.IrcLink + "'>" + this.formatIcon2(str) + "</a>";
	},

	formatChannelName: function (channel)
	{
		var str = channel.Name;
		if (channel.ErrorCode != "" && channel.ErrorCode != null && channel.ErrorCode != 0)
		{
			str += " - <small>" + channel.ErrorCode + "</small>";
		}
		return str;
	},

	/**********************************************************************************************************************/
	/* BOT FORMATER                                                                                                       */
	/**********************************************************************************************************************/

	formatBotIcon: function (bot)
	{
		var str = "Bot";

		if (bot.Connected != 1)
		{
			str += "_offline";
		}

		return "<a href='" + bot.IrcLink + "'>" + this.formatIcon(str) + "</a>";
	},

	/**
	 * @param {XGBot} bot
	 * @return {String}
	 */
	formatBotName: function (bot)
	{
		return bot.Name;
	},

	/**********************************************************************************************************************/
	/* PACKET FORMATER                                                                                                    */
	/**********************************************************************************************************************/

	formatPacketIcon: function (packet)
	{
		return "<a href='" + packet.IrcLink + "'>" + this.formatIcon("Packet") + "</a>";
	},

	formatPacketName: function (packet)
	{
		var ext = packet.Name.toLowerCase().substr(-3);
		var ret = "";
		if(ext == "avi" || ext == "wmv" || ext == "mkv")
		{
			ret += this.formatIcon("extension/video") + "&nbsp;&nbsp;";
		}
		else if(ext == "mp3")
		{
			ret += this.formatIcon("extension/audio") + "&nbsp;&nbsp;";
		}
		else if(ext == "rar" || ext == "tar" || ext == "zip")
		{
			ret += this.formatIcon("extension/compressed") + "&nbsp;&nbsp;";
		}
		else
		{
			ret += this.formatIcon("extension/default") + "&nbsp;&nbsp;";
		}

		if(packet.Name.toLowerCase().indexOf("german") > -1)
		{
			ret += this.formatIcon("language/de") + "&nbsp;&nbsp;";
		}

		ret += packet.Name;

		return ret;
	},

	formatPacketBotName: function (botName)
	{
		var ret = '<small>' + botName + '</small>';

		return ret;
	}
});
