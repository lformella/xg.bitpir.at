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

/* ************************************************************************** */
/* GLOBAL VARS / FUNCTIONS                                                    */
/* ************************************************************************** */

var id_server;
var id_channel;
var id_bot;
var id_packet;
var id_search;
var last_search;


/* ************************************************************************** */
/* GRID / FORM LOADER                                                         */
/* ************************************************************************** */

$(function ()
{
	/* ********************************************************************** */
	/* SERVER GRID                                                            */
	/* ********************************************************************** */

	jQuery("#servers").jqGrid(
		{
			url:"index.php?show=network&action=json&do=get_servers",
			datatype:"json",
			colNames:['', '', '', 'Name', 'LastModified', '', '', 'Channels', 'Bots', 'Packets', ''],
			colModel:[
				{name:'Guid', index:'Guid', hidden:true},
				{name:'Enabled', index:'Enabled', hidden:true},
				{name:'Connected', index:'Connected', width:26, formatter:FormatServerIcon, fixed:true},
				{name:'Name', index:'Name', formatter:FormatServerName},
				{name:'LastModified', index:'LastModified', hidden:true},
				{name:'Port', index:'Port', hidden:true},
				{name:'ErrorCode', index:'ErrorCode', hidden:true},
				{name:'ChannelCount', index:'ChannelCount', width:60, fixed:true, align:"right"},
				{name:'BotCount', index:'BotCount', width:60, fixed:true, align:"right"},
				{name:'PacketCount', index:'PacketCount', width:60, fixed:true, align:"right"},
				{name:'IrcLink', index:'IrcLink', hidden:true}
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

	/* ********************************************************************** */
	/* CHANNEL GRID                                                           */
	/* ********************************************************************** */

	jQuery("#channels").jqGrid(
		{
			//url: "index.php?show=network&action=json&do=get_channels_from_server&guid=" + guid,
			datatype:"json",
			colNames:['', '', '', '', 'Name', 'LastModified', '', 'Bots', 'Packets', ''],
			colModel:[
				{name:'ParentGuid', index:'ParentGuid', hidden:true},
				{name:'Guid', index:'Guid', hidden:true},
				{name:'Enabled', index:'Enabled', hidden:true},
				{name:'Connected', index:'Connected', width:26, formatter:FormatChannelIcon, fixed:true},
				{name:'Name', index:'Name', formatter:FormatChannelName},
				{name:'LastModified', index:'LastModified', hidden:true},
				{name:'ErrorCode', index:'ErrorCode', hidden:true},
				{name:'BotCount', index:'BotCount', width:60, fixed:true, align:"right"},
				{name:'PacketCount', index:'PacketCount', width:60, fixed:true, align:"right"},
				{name:'IrcLink', index:'IrcLink', hidden:true}
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

	/* ********************************************************************** */
	/* BOT GRID                                                               */
	/* ********************************************************************** */

	jQuery("#bots").jqGrid(
		{
			//url: "index.php?show=network&action=json&do=get_bots_from_channel&guid=" + guid,
			datatype:"json",
			colNames:['', '', '', '', 'Name', 'LastModified', '', 'Queue', '', 'Slots', '', 'Speed', '', 'LastContact', 'LastMessage', 'Packets', ''],
			colModel:[
				{name:'ParentGuid', index:'ParentGuid', hidden:true},
				{name:'Guid', index:'Guid', hidden:true},
				{name:'Enabled', index:'Enabled', hidden:true},
				{name:'Connected', index:'Connected', width:26, formatter:FormatBotIcon, fixed:true},
				{name:'Name', index:'Name', formatter:FormatBotName},
				{name:'LastModified', index:'LastModified', hidden:true},
				{name:'BotState', index:'BotState', hidden:true},
				{name:'InfoQueueCurrent', index:'InfoQueueCurrent', width:100, formatter:FormatBotQueue, fixed:true, align:"right"},
				{name:'InfoQueueTotal', index:'InfoQueueTotal', hidden:true},
				{name:'InfoSlotCurrent', index:'InfoSlotCurrent', width:100, formatter:FormatBotSlots, fixed:true, align:"right"},
				{name:'InfoSlotTotal', index:'InfoSlotTotal', hidden:true},
				{name:'InfoSpeedCurrent', index:'InfoSpeedCurrent', width:100, formatter:FormatBotSpeed, fixed:true, align:"right"},
				{name:'InfoSpeedMax', index:'InfoSpeedMax', hidden:true},
				{name:'LastContact', index:'LastContact', width:150, formatter:FormatTimeStamp, fixed:true, align:"right"},
				{name:'LastMessage', index:'LastMessage', hidden:true},
				{name:'PacketCount', index:'PacketCount', width:60, fixed:true, align:"right"},
				{name:'IrcLink', index:'IrcLink', hidden:true}
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

	/* ********************************************************************** */
	/* PACKET GRID                                                            */
	/* ********************************************************************** */

	jQuery("#packets").jqGrid(
		{
			//url: "index.php?show=network&action=json&do=get_packets_from_bot&guid=" + guid,
			datatype:"json",
			colNames:['', '', '', '', 'Id', 'Name', 'LastModified', 'LastUpdated', 'LastMentioned', 'Size', ''],
			colModel:[
				{name:'ParentGuid', index:'ParentGuid', hidden:true},
				{name:'Guid', index:'Guid', hidden:true},
				{name:'Enabled', index:'Enabled', hidden:true},
				{name:'Connected', index:'Connected', width:26, formatter:FormatPacketIcon, fixed:true},
				{name:'Id', index:'Id', width:38, formatter:FormatPacketId, fixed:true, align:"right"},
				{name:'Name', index:'Name', formatter:FormatPacketName},
				{name:'LastModified', index:'LastModified', hidden:true},
				{name:'LastUpdated', index:'LastUpdated', width:150, formatter:FormatTimeStamp, fixed:true, align:"right"},
				{name:'LastMentioned', index:'LastMentioned', width:150, formatter:FormatTimeStamp, fixed:true, align:"right"},
				{name:'Size', index:'Size', width:80, formatter:FormatSize, fixed:true, align:"right"},
				{name:'IrcLink', index:'IrcLink', hidden:true}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_packet = id;
					var obj = jQuery('#packets').getRowData(id);
					if (obj)
					{
						jQuery("#current_object").html(obj.IrcLink);
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

	/* ********************************************************************** */
	/* SEARCH GRID                                                            */
	/* ********************************************************************** */

	jQuery("#search").jqGrid(
		{
			//url: "index.php?show=network&action=json&do=get_packets_from_bot&guid=" + guid,
			datatype:"json",
			colNames:['', '', '', '', 'Id', 'Name', 'LastModified', 'LastUpdated', 'LastMentioned', 'Size', '', '', 'Speed'],
			colModel:[
				{name:'ParentGuid', index:'ParentGuid', hidden:true},
				{name:'Guid', index:'Guid', hidden:true},
				{name:'Enabled', index:'Enabled', hidden:true},
				{name:'Connected', index:'Connected', width:26, formatter:FormatPacketIcon, fixed:true},
				{name:'Id', index:'Id', width:38, formatter:FormatPacketId, fixed:true, align:"right"},
				{name:'Name', index:'Name', formatter:FormatPacketNameWithBotName},
				{name:'LastModified', index:'LastModified', hidden:true},
				{name:'LastUpdated', index:'LastUpdated', width:140, formatter:FormatTimeStamp, fixed:true, align:"right"},
				{name:'LastMentioned', index:'LastMentioned', width:140, formatter:FormatTimeStamp, fixed:true, align:"right"},
				{name:'Size', index:'Size', width:80, formatter:FormatSize, fixed:true, align:"right"},
				{name:'IrcLink', index:'IrcLink', hidden:true},
				{name:'BotName', index:'BotName', hidden:true},
				{name:'BotSpeed', index:'BotSpeed', width:100, formatter:FormatPacketSpeed, fixed:true, align:"right"}
			],
			onSelectRow:function (id)
			{
				if (id)
				{
					id_search = id;
					var obj = jQuery('#search').getRowData(id);
					if (obj)
					{
						jQuery("#current_object").html(obj.IrcLink);
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

	/* ********************************************************************** */
	/* OTHER STUFF                                                            */
	/* ********************************************************************** */

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
					jQuery("#current_object").html('&nbsp;');
					jQuery("#bread-server").fadeOut();
					jQuery("#bread-channel").fadeOut();
					jQuery("#bread-bot").fadeOut();
					trackPiwik(document.location, document.title);
					break;

				case 2:
					obj = jQuery('#servers').getRowData(id_server);
					if (obj)
					{
						jQuery("#current_object").html(obj.IrcLink);
						jQuery("#bread-server").html(obj.Connected + " " + obj.Name);
						jQuery("#bread-server").fadeIn();
						trackPiwik(document.location, document.title + " " + obj.Name);
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
						jQuery("#current_object").html(obj.IrcLink);
						jQuery("#bread-channel").html(obj.Connected + " " + obj.Name);
						jQuery("#bread-channel").fadeIn();
						trackPiwik(document.location, document.title + " " + obj.Name);
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
						jQuery("#current_object").html(obj.IrcLink);
						jQuery("#bread-channel").html(obj.Connected + " " + obj.Name);
						jQuery("#bread-channel").fadeIn();
					}
					obj = jQuery('#bots').getRowData(id_bot);
					if (obj)
					{
						jQuery("#bread-bot").html(obj.Connected + " " + obj.Name);
						jQuery("#bread-bot").fadeIn();
						trackPiwik(document.location, document.title + " " + obj.Name);
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

	$("#search-input").keyup(function (e)
	{
		if (e.which == 13)
		{
			DoSearch($(this).val());
		}
	});

	$('#search-input').delayedObserver(0.8,
		function (value, object)
		{
			DoSearch(value);
		});
});

/* ************************************************************************** */
/* DO SOMETHING                                                               */
/* ************************************************************************** */

function DoSearch(value)
{
	if(last_search != value)
	{
		last_search = value;
		jQuery("#search").clearGridData();
		jQuery("#search").setGridParam({url:"index.php?show=network&action=json&do=search_packets&searchString=" + value}).trigger("reloadGrid");

		trackPiwik(document.location, document.title + " " + value);
	}
}

/* ************************************************************************** */
/* SERVER FORMATER                                                            */
/* ************************************************************************** */

function FormatServerIcon (cellvalue, options, rowObject)
{
	var str = "Server";

	if (rowObject[2] == 1)
	{
		str += "";
	}
	else
	{
		str += "_disabled";
	}

	return "<a href='" + rowObject[10] + "'>" + FormatIcon(str) + "</a>";// + " " + rowObject[3];
}

function FormatServerName (cellvalue, options, rowObject)
{
	var str = rowObject[3] + ":" + rowObject[5];
	if (rowObject[6] != "" && rowObject[6] != null && rowObject[6] != 0)
	{
		str += " - <small>" + rowObject[6] + "</small>";
	}
	return str;
}

/* ************************************************************************** */
/* CHANNEL FORMATER                                                           */
/* ************************************************************************** */

function FormatChannelIcon (cellvalue, options, rowObject)
{
	var str = "Channel";

	if (rowObject[3] == 1)
	{
		str += "";
	}
	else if (rowObject[6] > 0)
	{
		str += "_error";
	}
	else
	{
		str += "_disabled";
	}

	return "<a href='" + rowObject[9] + "'>" + FormatIcon(str) + "</a>";// + " " + rowObject[3];
}

function FormatChannelName (cellvalue, options, rowObject)
{
	var str = rowObject[4];
	if (rowObject[6] != "" && rowObject[6] != null && rowObject[6] != 0)
	{
		str += " - <small>" + rowObject[6] + "</small>";
	}
	return str;
}

/* ************************************************************************** */
/* BOT FORMATER                                                               */
/* ************************************************************************** */

function FormatBotIcon (cellvalue, options, rowObject)
{
	var str = "Bot";

	if (rowObject[3] == 1)
	{
		str += "";
	}
	else
	{
		str += "_offline";
	}

	return "<a href='" + rowObject[16] + "'>" + FormatIcon(str) + "</a>";
}

function FormatBotName (cellvalue, options, rowObject)
{
	var str = rowObject[4];
	return str;

	if (rowObject[0] != undefined)
	{
		rowObject = Array2Bot(rowObject);
	}

	var ret = "";
	ret += cellvalue;
	if (/*rowObject.botstate != "Idle" &&*/ rowObject.lastmessage != "")
	{
		ret += "<br /><small><b>" + rowObject.lastcontact + ":</b> " + rowObject.lastmessage + "</small>";
	}
	return ret;
}

function FormatBotSpeed (cellvalue, options, rowObject)
{
	//if(rowObject[0] != undefined) { rowObject = Array2Bot(rowObject); }

	var ret = "";
	if(rowObject[11] != "")
	{
		ret += Speed2Human(rowObject[11]);
	}
	if(rowObject[11] != "" && rowObject[12] != "")
	{
		ret += " / ";
	}
	if(rowObject[12] != "")
	{
		ret += Speed2Human(rowObject[12]);
	}
	return ret;
}

function FormatBotSlots (cellvalue, options, rowObject)
{
	//if(rowObject[0] != undefined) { rowObject = Array2Bot(rowObject); }

	var ret = "";
	ret += rowObject[9];
	ret += " / ";
	ret += rowObject[10];
	return ret;
}

function FormatBotQueue (cellvalue, options, rowObject)
{
	//if(rowObject[0] != undefined) { rowObject = Array2Bot(rowObject); }

	var ret = "";
	ret += rowObject[7];
	ret += " / ";
	ret += rowObject[8];
	return ret;
}

/* ************************************************************************** */
/* PACKET FORMATER                                                            */
/* ************************************************************************** */

function FormatPacketIcon (cellvalue, options, rowObject)
{
	var str = "Packet";

	//if(rowObject[2] == 1) { str += ""; }
	//else { str += "_disabled"; }

	return "<a href='" + rowObject[10] + "'>" + FormatIcon(str) + "</a>";
}

function FormatPacketId (cellvalue, options, rowObject)
{
	var str = "#" + cellvalue;
	return str;
}

function FormatPacketName (cellvalue, options, rowObject)
{
//	if(rowObject[0] != undefined) { rowObject = Array2Packet(rowObject); }

	var ext = cellvalue.toLowerCase().substr(-3);
	var ret = "";
	if (ext == "avi" || ext == "wmv" || ext == "mkv")
	{
		ret += FormatIcon("extension/video") + "&nbsp;&nbsp;";
	}
	else if (ext == "mp3")
	{
		ret += FormatIcon("extension/audio") + "&nbsp;&nbsp;";
	}
	else if (ext == "rar" || ext == "tar" || ext == "zip")
	{
		ret += FormatIcon("extension/compressed") + "&nbsp;&nbsp;";
	}
	else
	{
		ret += FormatIcon("extension/default") + "&nbsp;&nbsp;";
	}

	if (cellvalue.toLowerCase().indexOf("german") > -1)
	{
		ret += FormatIcon("language/de") + "&nbsp;&nbsp;";
	}
	/*else
	 {
	 ret += FormatIcon("LanguageNone") + "&nbsp;&nbsp;";
	 }*/

	ret += cellvalue;

	return ret;
}

function FormatPacketNameWithBotName (cellvalue, options, rowObject)
{
	var ret = FormatPacketName(cellvalue, options, rowObject);
	ret += '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>via Bot ' + rowObject[11] + '</small>';

	return ret;
}

function FormatPacketSpeed (cellvalue, options, rowObject)
{
	var ret = "";
	if(cellvalue != "")
	{
		ret += Speed2Human(cellvalue);
	}
	return ret;
}

/* ************************************************************************** */
/* GLOBAL FORMATER                                                            */
/* ************************************************************************** */

function FormatIcon (img)
{
	return "<img src='images/" + img + ".png' />";
}

function FormatSize (cellvalue, options, rowObject)
{
	return Size2Human(cellvalue);
}

function FormatTime (cellvalue, options, rowObject)
{
	return Time2Human(cellvalue);
}

function FormatTimeStamp (cellvalue, options, rowObject)
{
	return TimeStampToHuman(cellvalue);
}

function FormatSpeed (cellvalue, options, rowObject)
{
	return Speed2Human(cellvalue);
}

function FormatInteger (cellvalue, options, rowObject)
{
	return cellvalue > 0 ? cellvalue : "";
}


/* ************************************************************************** */
/* HELPER                                                                     */
/* ************************************************************************** */

function Size2Human (size)
{
	if (size == 0)
	{
		return "";
	}
	if (size < 1024)
	{
		return size + " B";
	}
	else if (size < 1024 * 1024)
	{
		return (size / 1024).toFixed(0) + " KB";
	}
	else if (size < 1024 * 1024 * 1024)
	{
		return (size / (1024 * 1024)).toFixed(0) + " MB";
	}
	else
	{
		return (size / (1024 * 1024 * 1024)).toFixed(0) + " GB";
	}
}

function Speed2Image (speed)
{
	if (speed < 1024 * 125)
	{
		return "DL0";
	}
	else if (speed < 1024 * 250)
	{
		return "DL1";
	}
	else if (speed < 1024 * 500)
	{
		return "DL2";
	}
	else if (speed < 1024 * 750)
	{
		return "DL3";
	}
	else if (speed < 1024 * 1000)
	{
		return "DL4";
	}
	else
	{
		return "DL5";
	}
}

function TimeStampToDate (timestamp)
{
	var date = new Date(timestamp * 1000);
	date.setHours(date.getHours() - 2);
	return date;
}

var LANG_MONTH = new Array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
var LANG_WEEKDAY = new Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");

function TimeStampToHuman (timestamp)
{
	if (timestamp <= 0)
	{
		return "";
	}

	var date = TimeStampToDate(timestamp);
	var diff = (((new Date()).getTime() - date.getTime()) / 1000);

	if (diff < 0)
	{
		return "now";
	}
	if (diff < 60)
	{
		diff = Math.floor(diff);
		return diff + " second(s) ago";
	}
	diff = diff / 60;
	if (diff < 60)
	{
		diff = Math.floor(diff);
		return diff + " minute(s) ago";
	}
	diff = diff / 60;
	if (diff < 24)
	{
		diff = Math.floor(diff);
		return diff + " hour(s) ago";
	}

	var hours = date.getHours();
	if (hours < 10)
	{
		hours = "0" + hours;
	}
	var minutes = date.getMinutes();
	if (minutes < 10)
	{
		minutes = "0" + minutes;
	}

	diff = diff / 24;
	if (diff < 2)
	{
		return "yesterday at " + hours + ":" + minutes + "";
	}
	if (diff < 7)
	{
		return LANG_WEEKDAY[date.getDay()] + " at " + hours + ":" + minutes + "";
	}

	return date.getDate() + ". " + LANG_MONTH[date.getMonth()] + " at " + hours + ":" + minutes + "";
}

function Speed2Human (speed)
{
	if (speed == 0)
	{
		return "";
	}
	if (speed < 1024)
	{
		return speed + " B";
	}
	else if (speed < 1024 * 1024)
	{
		return (speed / 1024).toFixed(2) + " KB";
	}
	else
	{
		return (speed / (1024 * 1024)).toFixed(2) + " MB";
	}
}

function Time2Human (time)
{
	var str = "";
	if (time < 0 || time >= 106751991167300 || time == "106751991167300")
	{
		return str;
	}

	var buff = 0;

	if (time > 86400)
	{
		buff = Math.floor(time / 86400);
		str += (buff >= 10 ? "" + buff : "0" + buff) + ":";

		time -= buff * 86400;
	}
	else if (str != "")
	{
		str += "00:";
	}

	if (time > 3600)
	{
		buff = Math.floor(time / 3600);
		str += (buff >= 10 ? "" + buff : "0" + buff) + ":";
		time -= buff * 3600;
	}
	else if (str != "")
	{
		str += "00:";
	}

	if (time > 60)
	{
		buff = Math.floor(time / 60);
		str += (buff >= 10 ? "" + buff : "0" + buff) + ":";
		time -= buff * 60;
	}
	else if (str != "")
	{
		str += "00:";
	}

	if (time > 0)
	{
		buff = time;
		str += (buff >= 10 ? "" + buff : "0" + buff);
		time -= buff;
	}
	else if (str != "")
	{
		str += "00";
	}

	return str;
}

function trackPiwik(url, title)
{
	if(title == undefined)
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
	catch(err)
	{
	}
}
