<?php
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

namespace XG\Classes\Controller;

use XG\Classes\Domain\Service;
use XG\Classes\Domain\Sorter;
use XG\Classes\View;

class Network extends Base
{
	/**
	 * @param array $request
	 * @param Service $service
	 */
	public function __construct (array $request, Service $service)
	{
		parent::__construct($request, $service);
	}

	/**
	 * @return string
	 */
	public function display ()
	{
		$action = isset($this->request['action']) && !empty($this->request['action']) ? $this->request['action'] : 'index';

		switch ($action)
		{
			case 'json':
				return $this->jsonAction();

			case 'index':
			default:
				return $this->indexAction();
		}
	}

	/**
	 * @return string
	 */
	private function indexAction ()
	{
		$view = new View();
		$content = $view->loadTemplate('index', __CLASS__);

		$view = new View();
		$view->assign('title', 'Networks');
		$view->assign('content', $content);
		$content = $view->loadTemplate('page');

		return $content;
	}

	/**
	 * @return string
	 */
	private function jsonAction ()
	{
		$start = ($this->request['page'] - 1) * $this->request['rows'];
		$end = $start + $this->request['rows'];

		$view = new View();

		$sort = new Sorter();

		$objects = array();
		$json_objects = array();

		switch ($this->request['do'])
		{
			case "get_servers" :
				$objects = $this->service->GetServers();
				$sort->Sort($objects, $this->request['sidx'], $this->request['sord'] == "desc");
				$i = 0;
				foreach ($objects as $object)
				{
					if ($i >= $start && $i < $end)
					{
						$arr = array();
						$arr['id'] = $object->Guid;
						$arr['cell'] = array(
							$object->Guid,
							(int)$object->Enabled,
							(int)$object->Connected,
							$object->Name,
							(int)$object->LastModified,
							(int)$object->Port,
							(int)$object->ErrorCode,
							(int)$object->ChannelCount,
							(int)$object->BotCount,
							(int)$object->PacketCount,
							$object->IrcLink
						);
						$json_objects[] = $arr;
					}
					$i++;
				}
				break;

			case "get_channels_from_server" :
				$objects = $this->service->GetChannelsFromServer($this->request['guid']);
				$sort->Sort($objects, $this->request['sidx'], $this->request['sord'] == "desc");
				$i = 0;
				foreach ($objects as $object)
				{
					if ($i >= $start && $i < $end)
					{
						$arr = array();
						$arr['id'] = $object->Guid;
						$arr['cell'] = array(
							$object->ParentGuid,
							$object->Guid,
							(int)$object->Enabled,
							(int)$object->Connected,
							$object->Name,
							(int)$object->LastModified,
							(int)$object->ErrorCode,
							(int)$object->BotCount,
							(int)$object->PacketCount,
							$object->IrcLink
						);
						$json_objects[] = $arr;
					}
					$i++;
				}
				break;

			case "get_bots_from_channel" :
				$objects = $this->service->GetBotsFromChannel($this->request['guid']);
				$sort->Sort($objects, $this->request['sidx'], $this->request['sord'] == "desc");
				$i = 0;
				foreach ($objects as $object)
				{
					if ($i >= $start && $i < $end)
					{
						$arr = array();
						$arr['id'] = $object->Guid;
						$arr['cell'] = array(
							$object->ParentGuid,
							$object->Guid,
							(int)$object->Enabled,
							(int)$object->Connected,
							$object->Name,
							(int)$object->LastModified,
							(int)$object->BotState,
							(int)$object->InfoQueueCurrent,
							(int)$object->InfoQueueTotal,
							(int)$object->InfoSlotCurrent,
							(int)$object->InfoSlotTotal,
							(float)$object->InfoSpeedCurrent,
							(float)$object->InfoSpeedMax,
							(int)$object->LastContact,
							$object->LastMessage,
							(int)$object->PacketCount,
							$object->IrcLink
						);
						$json_objects[] = $arr;
					}
					$i++;
				}
				break;

			case "get_packets_from_bot" :
				$objects = $this->service->GetPacketsFromBot($this->request['guid']);
				$sort->Sort($objects, $this->request['sidx'], $this->request['sord'] == "desc");
				$i = 0;
				foreach ($objects as $object)
				{
					if ($i >= $start && $i < $end)
					{
						$arr = array();
						$arr['id'] = $object->Guid;
						$arr['cell'] = array(
							$object->ParentGuid,
							$object->Guid,
							(int)$object->Enabled,
							(int)$object->Connected,
							(int)$object->Id,
							$object->Name,
							(int)$object->LastModified,
							(int)$object->LastUpdated,
							(int)$object->LastMentioned,
							(int)$object->Size,
							$object->IrcLink
						);
						$json_objects[] = $arr;
					}
					$i++;
				}
				break;

			case "search_packets" :
				if (strlen($this->request['searchString']) >= 3)
				{
					$strings = explode(" ", $this->request['searchString']);
					$objects = $this->service->SearchPackets($strings);

					$sort->Sort($objects, $this->request['sidx'], $this->request['sord'] == "desc");
					$i = 0;
					foreach ($objects as $object)
					{
						if ($i >= $start && $i < $end)
						{
							$arr = array();
							$arr['id'] = $object->Guid;
							$arr['cell'] = array(
								$object->ParentGuid,
								$object->Guid,
								(int)$object->Enabled,
								(int)$object->Connected,
								(int)$object->Id,
								$object->Name,
								(int)$object->LastModified,
								(int)$object->LastUpdated,
								(int)$object->LastMentioned,
								(int)$object->Size,
								$object->IrcLink,
								$object->BotName,
								$object->BotSpeed
							);
							$json_objects[] = $arr;
						}
						$i++;
					}
				}
				break;
		}

		$objectsCount = sizeof($objects);
		$json = array();
		$json['page'] = $this->request['page'];
		$json['total'] = ceil($objectsCount / $this->request['rows']);
		$json['records'] = $objectsCount;
		$json['rows'] = $json_objects;

		$view->assign('json', $json);
		$content = $view->loadTemplate('json', __CLASS__);

		return $content;
	}
}
