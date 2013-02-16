//
//  xg.formatter.js
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

var XGFormatter = Class.create(
{
	initialize: function (helper)
	{
		this.helper = helper;
	},

	/* ************************************************************************************************************** */
	/* SERVER FORMATER                                                                                                */
	/* ************************************************************************************************************** */

	formatServerIcon: function (server, onclick)
	{
		var icon = "book";
		var iconClass = "Aluminium2Middle";
		var overlay = "";
		var overlayClass = "";
		var overlayStyle = "opacity: 0.6";

		if (!server.enabled)
		{
			iconClass = "Aluminium1Dark";
		}
		else if (server.connected)
		{
			overlay = "ok-circle2";
			overlayClass = "ChameleonMiddle";
		}
		else if (server.errorCode != "" && server.errorCode != "None" && server.errorCode != "0")
		{
			overlay = "attention";
			overlayClass = "ScarletRedMiddle";
		}

		return this.formatIcon2(icon, iconClass, overlay, overlayClass, overlayStyle, onclick);
	},

	formatChannelIcon: function (channel, onclick)
	{
		var icon = "folder";
		var iconClass = "Aluminium2Middle";
		var overlay = "";
		var overlayClass = "";
		var overlayStyle = "opacity: 0.6";

		if (!channel.enabled)
		{
			iconClass = "Aluminium1Dark";
		}
		else if (channel.connected)
		{
			overlay = "ok-circle2";
			overlayClass = "ChameleonMiddle";
		}
		else if (channel.errorCode != "" && channel.errorCode != "None" && channel.errorCode != "0")
		{
			overlay = "attention";
			overlayClass = "ScarletRedMiddle";
		}

		return this.formatIcon2(icon, iconClass, overlay, overlayClass, overlayStyle, onclick);
	},

	/* ************************************************************************************************************** */
	/* SEARCH FORMATER                                                                                                */
	/* ************************************************************************************************************** */

	formatSearchIcon: function (cellvalue)
	{
		var icon = "search";
		var iconClass = "Aluminium2Middle";

		switch (cellvalue)
		{
			case "1":
				icon = "clock";
				iconClass = "OrangeMiddle";
				break;

			case "2":
				icon = "clock";
				iconClass = "ButterMiddle";
				break;

			case "3":
				icon = "down-circle2";
				iconClass = "SkyBlueMiddle";
				break;

			case "4":
				icon = "ok-circle2";
				iconClass = "ChameleonMiddle";
				break;
		}
		return this.formatIcon2(icon, iconClass);
	},

	/* ************************************************************************************************************** */
	/* BOT FORMATER                                                                                                   */
	/* ************************************************************************************************************** */

	formatBotIcon: function (bot)
	{
		var icon = "user";
		var iconClass = "Aluminium2Middle";
		var overlay = "";
		var overlayClass = "";
		var overlayStyle = "";

		if (!bot.connected)
		{
			iconClass = "Aluminium1Dark";
		}
		else
		{
			switch (bot.state)
			{
				case 0:
					if (bot.infoSlotCurrent > 0)
					{
						overlay = "ok-circle2";
						overlayClass = "ChameleonMiddle";
						overlayStyle = "opacity: 0.6";
					}
					else if (bot.infoSlotCurrent == 0 && bot.infoSlotCurrent)
					{
						overlay = "cancel-circle2";
						overlayClass = "OrangeMiddle";
					}
					break;

				case 1:
					iconClass = "SkyBlueDark";
					overlay = "down-circle2";
					overlayClass = "SkyBlueMiddle";
					overlayStyle = "opacity: " + this.speed2Overlay(bot.speed);
					break;

				case 2:
					overlay = "clock";
					overlayClass = "OrangeMiddle";
					break;
			}
		}

		return this.formatIcon2(icon, iconClass, overlay, overlayClass, overlayStyle);
	},

	formatBotName: function (bot)
	{
		var ret = bot.name;
		if (bot.lastMessage != "")
		{
			ret += "<br /><small><b>" + bot.lastContact + ":</b> " + bot.lastMessage + "</small>";
		}
		return ret;
	},

	formatBotSpeed: function (bot)
	{
		var ret = "";
		if (bot.infoSpeedCurrent > 0)
		{
			ret += this.helper.speed2Human(bot.infoSpeedCurrent);
		}
		if (bot.infoSpeedCurrent > 0 && bot.infoSpeedMax > 0)
		{
			ret += " / ";
		}
		if (bot.infoSpeedMax > 0)
		{
			ret += this.helper.speed2Human(bot.infoSpeedMax);
		}
		return ret;
	},

	formatBotSlots: function (bot)
	{
		var ret = "";
		ret += bot.infoSlotCurrent;
		ret += " / ";
		ret += bot.infoSlotTotal;
		return ret;
	},

	formatBotQueue: function (bot)
	{
		var ret = "";
		ret += bot.infoQueueCurrent;
		ret += " / ";
		ret += bot.infoQueueTotal;
		return ret;
	},

	/* ************************************************************************************************************** */
	/* PACKET FORMATER                                                                                                */
	/* ************************************************************************************************************** */

	formatPacketIcon: function (packet, onclick, skipOverlay)
	{
		var icon = "gift";
		var iconClass = "Aluminium2Middle";
		var overlay = "";
		var overlayClass = "";
		var overlayStyle = "";

		if (!packet.enabled)
		{
			iconClass = "Aluminium1Dark";
		}
		else if (!skipOverlay)
		{
			if (packet.connected)
			{
				iconClass = "SkyBlueDark";
				overlay = "down-circle2";
				overlayClass = "SkyBlueMiddle";
				overlayStyle = "opacity: " + this.speed2Overlay(packet.part != null ? packet.part.speed : 0);
			}
			else if (packet.next)
			{
				overlay = "clock";
				overlayClass = "OrangeMiddle";
			}
			else
			{
				overlay = "clock";
				overlayClass = "ButterMiddle";
			}
		}

		return this.formatIcon2(icon, iconClass, overlay, overlayClass, overlayStyle, onclick);
	},

	formatPacketId: function (packet)
	{
		return "#" + packet.id;
	},

	formatPacketName: function (packet)
	{
		var name = packet.name;

		if (name == undefined)
		{
			return "";
		}

		var icon = "doc";
		var iconClass = "Aluminium2Middle";

		var ext = name.toLowerCase().substr(-3);
		var ret = "";
		if (ext == "avi" || ext == "wmv" || ext == "mkv" || ext == "mpg")
		{
			icon = "video-1";
		}
		else if (ext == "mp3")
		{
			icon = "headphones";
		}
		else if (ext == "rar" || ext == "tar" || ext == "zip")
		{
			icon = "th";
		}
		ret += this.formatIcon(icon, iconClass) + "&nbsp;&nbsp;" + name;

		return ret;
	},

	formatPacketSpeed: function (packet)
	{
		return this.helper.speed2Human(packet.part != null ? packet.part.speed : 0);
	},

	formatPacketSize: function (packet)
	{
		return this.helper.size2Human(packet.size);
	},

	formatPacketTimeMissing: function (packet)
	{
		return this.helper.time2Human(packet.part != null ? packet.part.timeMissing : 0);
	},

	/* ************************************************************************************************************** */
	/* IMAGE FORMATER                                                                                                 */
	/* ************************************************************************************************************** */

	speed2Overlay: function (speed)
	{
		var opacity = (speed / (1024 * 1500)) * 0.4;
		return (opacity > 0.4 ? 0.4 : opacity) + 0.6;
	},

	formatIcon: function (icon, iconClass)
	{
		iconClass = "icon-medium icon-" + icon + " " + iconClass;
		return "<i class='" + iconClass + "'></i>";
	},

	formatIcon2: function (icon, iconClass, overlay, overlayClass, overlayStyle, onclick)
	{
		iconClass = "icon-big icon-" + icon + " " + iconClass;
		if (onclick != undefined && onclick != "")
		{
			iconClass += " button";
		}
		overlayClass = "icon-overlay icon-" + overlay + " " + overlayClass;

		var str = "";
		if (overlay != undefined && overlay != "")
		{
			str += "<i class='" + overlayClass + "'";
			if (overlayStyle != undefined && overlayStyle != "")
			{
				str += " style='" + overlayStyle + "'";
			}
			str += "></i>";
		}
		str += "<i class='" + iconClass + "'";
		if (onclick != undefined && onclick != "")
		{
			str += " onclick='" + onclick + "'";
		}
		str += "></i>";

		return str;
	}
});
