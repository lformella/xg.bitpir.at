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
/* CONTROLLER                                                                                                         */
/**********************************************************************************************************************/

var BaseController = Class.create(
{
	initialize: function()
	{
		this.helper = new XGHelper();
		this.formatter = new MyFormatter(this.helper);

		$("#searchInput2").keyup(function (e)
		{
			if (e.which == 13)
			{
				if(!$("#search").length)
				{
					window.location = '?show=search#' + $(this).val();
				}
			}
		});
	},

	/**
	 * @param {String} grid
	 * @param {String} guid
	 * @return {object}
	 */
	getRowData: function (grid, guid)
	{
		var rowData = $("#" + grid).getRowData(guid);
		return $.parseJSON(rowData.Object);
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
	},

	trackPiwikSearch: function (keyword, category, results)
	{
		try
		{
			piwikTracker.trackSiteSearch(keyword, category, results);
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

		this.id_server = 0;
		this.id_channel = 0;
		this.id_bot = 0;
		this.id_packet = 0;

		/**************************************************************************************************************/
		/* SERVER GRID                                                                                                */
		/**************************************************************************************************************/

		$("#servers").jqGrid(
		{
			url:"index.php?show=network&action=json&do=get_servers",
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', '', 'Name', 'Channels', 'Bots', 'Packets'],
			colModel:[
				{name:'Object',			index:'object',			formatter: function(c, o, r) { return JSON.stringify(r); }, hidden:true},
				{name:'Icon',			index:'icon',			formatter: function(c, o, r) { return self.formatter.formatServerIcon2(r, r.ircLink); }, width:38, classes: "icon-cell"},
				{name:'Name',			index:'name',			formatter: function(c, o, r) { return self.formatter.formatServerName(r); }, fixed:false},
				{name:'ChannelCount',	index:'channelCount',	formatter: function(c, o, r) { return r.channelCount; }, width:60, align:"right"},
				{name:'BotCount',		index:'botCount',		formatter: function(c, o, r) { return r.botCount; }, width:60, align:"right"},
				{name:'PacketCount',	index:'packetCount',	formatter: function(c, o, r) { return r.packetCount; }, width:60, align:"right"}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					self.id_server = id;
					var obj = self.getRowData("servers", id);
					if (obj)
					{
						$("#channels").clearGridData();
						$("#channels").setGridParam({url:"index.php?show=network&action=json&do=get_channels_from_server&guid=" + obj.guid}).trigger("reloadGrid");
						networkSlider.goToSlide(2);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:$('#servers-pager'),
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

		$("#channels").jqGrid(
		{
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', '', 'Name', 'Bots', 'Packets'],
			colModel:[
				{name:'Object',			index:'object',			formatter: function(c, o, r) { return JSON.stringify(r); }, hidden:true},
				{name:'Icon',			index:'icon',			formatter: function(c, o, r) { return self.formatter.formatChannelIcon2(r); }, width:38, classes: "icon-cell"},
				{name:'Name',			index:'name',			formatter: function(c, o, r) { return self.formatter.formatChannelName(r); }, fixed:false},
				{name:'BotCount',		index:'botCount',		formatter: function(c, o, r) { return r.botCount; }, width:60, align:"right"},
				{name:'PacketCount',	index:'packetCount',	formatter: function(c, o, r) { return r.packetCount; }, width:60, align:"right"}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					self.id_channel = id;
					var obj = self.getRowData("channels", id);
					if (obj)
					{
						$("#bots").clearGridData();
						$("#bots").setGridParam({url:"index.php?show=network&action=json&do=get_bots_from_channel&guid=" + obj.guid}).trigger("reloadGrid");
						networkSlider.goToSlide(3);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:$('#channels-pager'),
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

		$("#bots").jqGrid(
		{
			//url: "index.php?show=network&action=json&do=get_bots_from_channel&guid=" + guid,
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', '', 'Name', 'Queue', 'Slots', 'Speed', 'Last Contact', 'Packets'],
			colModel:[
				{name:'Object',				index:'object',				formatter: function(c, o, r) { return JSON.stringify(r); }, hidden:true},
				{name:'Icon',				index:'icon',				formatter: function(c, o, r) { return self.formatter.formatBotIcon(r); }, width:38, classes: "icon-cell"},
				{name:'Name',				index:'name',				formatter: function(c, o, r) { return self.formatter.formatBotName(r); }, fixed:false},
				{name:'InfoQueueCurrent',	index:'infoQueueCurrent',	formatter: function(c, o, r) { return self.formatter.formatBotQueue(r); }, width:80, align:"right"},
				{name:'InfoSlotCurrent',	index:'infoSlotCurrent',	formatter: function(c, o, r) { return self.formatter.formatBotSlots(r); }, width:80, align:"right"},
				{name:'InfoSpeedCurrent',	index:'infoSpeedCurrent',	formatter: function(c, o, r) { return self.formatter.formatBotSpeed(r); }, width:120, align:"right"},
				{name:'LastContact',		index:'lastContact',		formatter: function(c, o, r) { return self.helper.date2Human(r.lastContact); }, width:150, align:"right"},
				{name:'PacketCount',		index:'packetCount',		formatter: function(c, o, r) { return r.packetCount; }, width:60, align:"right"}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					self.id_bot = id;
					var obj = self.getRowData("bots", id);
					if (obj)
					{
						$("#packets").clearGridData();
						$("#packets").setGridParam({url:"index.php?show=network&action=json&do=get_packets_from_bot&guid=" + obj.guid}).trigger("reloadGrid");
						networkSlider.goToSlide(4);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:$('#bots-pager'),
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

		$("#packets").jqGrid(
		{
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', '', 'Id', 'Name', 'Last Mentioned', 'Size'],
			colModel:[
				{name:'Object',			index:'object',			formatter: function(c, o, r) { return JSON.stringify(r); }, hidden:true},
				{name:'Icon',			index:'icon',			formatter: function(c, o, r) { return self.formatter.formatPacketIcon2(r); }, width:38, classes: "icon-cell"},
				{name:'Id',				index:'id',				formatter: function(c, o, r) { return self.formatter.formatPacketId(r); }, width:38, align:"right"},
				{name:'Name',			index:'name',			formatter: function(c, o, r) { return self.formatter.formatPacketName(r); }, fixed:false},
				{name:'LastMentioned',	index:'lastMentioned',	formatter: function(c, o, r) { return self.helper.date2Human(r.lastMentioned); }, width:150, align:"right"},
				{name:'Size',			index:'size',			formatter: function(c, o, r) { return self.helper.size2Human(r.size); }, width:80, align:"right"}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					self.id_packet = id;
					var obj = self.getRowData("packets", id);
					if (obj)
					{
						$("#ircLink").html(obj.ircLink);
					}
				}
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:$('#packets-pager'),
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
			autoheight:false,
			numericText:['servers', 'channels', 'bots', 'packets'],
			beforeAniFunc:function (t)
			{
				var obj;
				switch (t)
				{
					case 1:
						$("#ircLink").html('&nbsp;');
						$("#bread-server").fadeOut();
						$("#bread-channel").fadeOut();
						$("#bread-bot").fadeOut();
						break;

					case 2:
						obj = self.getRowData("servers", self.id_server);
						if (obj)
						{
							$("#ircLink").html(obj.ircLink);
							$("#bread-server-name").html(obj.name);
							$("#bread-server").fadeIn();
							self.trackPiwik(document.location, document.title + " " + obj.name);
						}
						$("#bread-channel").fadeOut();
						$("#bread-bot").fadeOut();
						break;

					case 3:
						obj = self.getRowData("servers", self.id_server);
						if (obj)
						{
							$("#bread-server-name").html(obj.name);
							$("#bread-server").fadeIn();
						}
						obj = self.getRowData("channels", self.id_channel);
						if (obj)
						{
							$("#ircLink").html(obj.ircLink);
							$("#bread-channel-name").html(obj.name);
							$("#bread-channel").fadeIn();
							self.trackPiwik(document.location, document.title + " " + obj.name);
						}
						$("#bread-bot").fadeOut();
						break;

					case 4:
						obj = self.getRowData("servers", self.id_server);
						if (obj)
						{
							$("#bread-server-name").html(obj.name);
							$("#bread-server").fadeIn();
						}
						obj = self.getRowData("channels", self.id_channel);
						if (obj)
						{
							$("#ircLink").html(obj.ircLink);
							$("#bread-channel-name").html(obj.name);
							$("#bread-channel").fadeIn();
						}
						obj = self.getRowData("bots", self.id_bot);
						if (obj)
						{
							$("#bread-bot-name").html(obj.name);
							$("#bread-bot").fadeIn();
							self.trackPiwik(document.location, document.title + " " + obj.name);
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

		this.id_search = 0;
		this.current_search = "";
		this.last_search = "";

		/**************************************************************************************************************/
		/* SEARCH GRID                                                                                                */
		/**************************************************************************************************************/

		$("#search").jqGrid(
		{
			datatype:"json",
			cmTemplate:{fixed:true},
			colNames:['', '', 'Id', 'Name', 'Last Mentioned', 'Size', 'Bot', 'Speed'],
			colModel:[
				{name:'Object',			index:'object',			formatter: function(c, o, r) { return JSON.stringify(r); }, hidden:true},
				{name:'Connected',		index:'connected',		formatter: function(c, o, r) { return self.formatter.formatPacketIcon2(r); }, width:38, classes: "icon-cell"},
				{name:'Id',				index:'id',				formatter: function(c, o, r) { return self.formatter.formatPacketId(r); }, width:38, align:"right"},
				{name:'Name',			index:'name',			formatter: function(c, o, r) { return self.formatter.formatPacketName(r); }, fixed:false},
				{name:'LastMentioned',	index:'lastMentioned',	formatter: function(c, o, r) { return self.helper.date2Human(r.lastMentioned); }, width:140, align:"right"},
				{name:'Size',			index:'size',			formatter: function(c, o, r) { return self.helper.size2Human(r.size); }, width:60, align:"right"},
				{name:'BotName',		index:'botName',		formatter: function(c, o, r) { return self.formatter.formatPacketBotName(r.botName); }, width:100},
				{name:'BotSpeed',		index:'botSpeed',		formatter: function(c, o, r) { return self.helper.speed2Human(r.botSpeed); }, width:80, align:"right"}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					self.id_search = id;
					var obj = self.getRowData("search", id);
					if (obj)
					{
						$("#ircLink").html(obj.ircLink);
					}
				}
			},
			loadComplete:function(data)
			{
				self.trackPiwikSearch(self.current_search, false, data.records);
			},
			rowNum:20,
			rowList:[20, 40, 80, 160],
			pager:$('#search-pager'),
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

		if (this.last_search != searchUrl)
		{
			this.last_search = searchUrl;

			$("#search").clearGridData();
			$("#search").setGridParam({url: searchUrl}).trigger("reloadGrid");

			this.current_search = value;
		}
	}
});

/**********************************************************************************************************************/
/* CUSTOM FORMATER                                                                                                    */
/**********************************************************************************************************************/

var MyFormatter = Class.create(XGFormatter,
{
	initialize: function($super, helper)
	{
		$super(helper);
	},

	/**********************************************************************************************************************/
	/* SERVER FORMATER                                                                                                    */
	/**********************************************************************************************************************/

	formatServerIcon2: function (server)
	{
		var ret = this.formatServerIcon(server);
		return "<a href='" + server.ircLink + "' target='_blank'>" + ret + "</a>";
	},

	formatServerName: function (server)
	{
		var str = server.name;
		if (server.errorCode != "" && server.errorCode != null && server.errorCode != 0)
		{
			str += " - <small>" + server.errorCode + "</small>";
		}
		return str;
	},

	/**********************************************************************************************************************/
	/* CHANNEL FORMATER                                                                                                   */
	/**********************************************************************************************************************/

	formatChannelIcon2: function (channel)
	{
		var ret = this.formatChannelIcon(channel);
		return "<a href='" + channel.ircLink + "' target='_blank'>" + ret + "</a>";
	},

	formatChannelName: function (channel)
	{
		var str = channel.name;
		if (channel.errorCode != "" && channel.errorCode != null && channel.errorCode != 0)
		{
			str += " - <small>" + channel.errorCode + "</small>";
		}
		return str;
	},

	/******************************************************************************************************************/
	/* BOT FORMATER                                                                                                   */
	/******************************************************************************************************************/

	formatBotName: function (bot)
	{
		var ret = bot.name;
		if (bot.lastMessage != "")
		{
			ret += "<br /><small><b>" + this.helper.date2Human(bot.lastContact) + ":</b> " + bot.lastMessage + "</small>";
		}
		return ret;
	},

	/**********************************************************************************************************************/
	/* PACKET FORMATER                                                                                                    */
	/**********************************************************************************************************************/

	formatPacketIcon2: function (packet)
	{
		var ret = this.formatPacketIcon(packet);
		return "<a href='" + packet.ircLink + "' target='_blank'>" + ret + "</a>";
	},

	formatPacketBotName: function (botName)
	{
		var ret = '<small>' + botName + '</small>';

		return ret;
	}
});
