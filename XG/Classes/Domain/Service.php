<?php
//
//  Service.php
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

namespace XG\Classes\Domain;

use PDO;
use XG\Classes\Domain\Model\Base;
use XG\Classes\Domain\Model\Bot;
use XG\Classes\Domain\Model\Channel;
use XG\Classes\Domain\Model\Packet;
use XG\Classes\Domain\Model\PacketSearch;
use XG\Classes\Domain\Model\Server;
use XG\Classes\Domain\Model\SearchOption;
use XG\Classes\Domain\Model\Snapshot;

class Service
{
	/** @var PDO */
	protected $pdo;

	/**
	 * @param PDO $pdo
	 *
	 * @return Service
	 */
	public function __construct (PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * @param Base[] $objects
	 * @return Base[]
	 */
	public function FixObjects (array $objects)
	{
		foreach($objects as $object)
		{
			$object->Connected = (int)$object->Connected == 1 ? true : false;
			$object->Enabled = (int)$object->Enabled == 1 ? true : false;
		}
		return $objects;
	}

	/**
	 * @return Server[]
	 */
	public function GetServers ()
	{
		$stmt = $this->pdo->prepare("
			SELECT *, CONCAT('irc://', name, ':', port, '/') AS IrcLink
			FROM servers;
		");
		$stmt->execute();
		/** @var $result Server[] */
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, 'XG\Classes\Domain\Model\Server');

		return !$result ? array() : $this->FixObjects($result);
	}

	/**
	 * @param  string $guid
	 *
	 * @return Channel[]
	 */
	public function GetChannelsFromServer ($guid)
	{
		$stmt = $this->pdo->prepare("
			SELECT c.*, CONCAT('irc://', s.name, ':', s.port, '/', c.name, '/') AS IrcLink
			FROM channels c
			INNER JOIN servers s ON s.guid = c.parentguid
			WHERE c.parentguid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, 'XG\Classes\Domain\Model\Channel');

		return !$result ? array() : $this->FixObjects($result);
	}

	/**
	 * @param  string $guid
	 *
	 * @return Bot[]
	 */
	public function GetBotsFromChannel ($guid)
	{
		$stmt = $this->pdo->prepare("
			SELECT b.*, CONCAT('irc://', s.name, ':', s.port, '/', c.name, '/') AS IrcLink
			FROM bots b
			INNER JOIN channels c ON c.guid = b.parentguid
			INNER JOIN servers s ON s.guid = c.parentguid
			WHERE b.parentguid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, 'XG\Classes\Domain\Model\Bot');

		return !$result ? array() : $this->FixObjects($result);
	}

	/**
	 * @param  string $guid
	 *
	 * @return Packet[]
	 */
	public function GetPacketsFromBot ($guid)
	{
		$stmt = $this->pdo->prepare("
			SELECT p.*, CONCAT('xdcc://', s.name, '/', s.name, '/', c.name, '/', b.name, '/#', p.id, '/', p.name, '/') AS IrcLink
			FROM packets p
			INNER JOIN bots b ON b.guid = p.parentguid
			INNER JOIN channels c ON c.guid = b.parentguid
			INNER JOIN servers s ON s.guid = c.parentguid
			WHERE p.parentguid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, 'XG\Classes\Domain\Model\Packet');

		return !$result ? array() : $this->FixObjects($result);
	}

	/**
	 * @param  SearchOption $searchOptions
	 *
	 * @return PacketSearch[]
	 */
	public function SearchPackets ($searchOptions)
	{
		$strings = explode(' ', $searchOptions->Name);
		foreach($strings as $key => $string)
		{
			$string = trim($string);
			if($string == '')
			{
				unset($strings[$key]);
			}
		}
		#sort($strings);
		$this->AddSearch(implode($strings, ' '));

		$count = 0;
		$str = '';
		foreach ($strings as $string)
		{
			if ($str != '')
			{
				$str .= ' AND ';
			}
			$str .= ' p.name LIKE :string' . $count++ . ' ';
		}

		if($searchOptions->MaxSize > 0)
		{
			$str .= ' AND p.size < :maxSize ';
		}
		if($searchOptions->MinSize > 0)
		{
			$str .= ' AND p.size > :minSize ';
		}
		if($searchOptions->LastMentioned > 0)
		{
			$str .= ' AND p.lastMentioned > :lastMentioned ';
		}

		switch($searchOptions->BotState)
		{
			case 0:
				break;
			case 1:
				$str .= ' AND b.infoSlotCurrent > 0';
				break;
			case 2:
				$str .= ' AND (b.infoSlotCurrent > 0 OR b.infoQueueCurrent > 0)';
				break;
			case 3:
				$str .= ' AND b.Connected == 1';
				break;
		}

		$stmt = $this->pdo->prepare("
			SELECT p.*, CONCAT('xdcc://', s.name, '/', s.name, '/', c.name, '/', b.name, '/#', p.id, '/', p.name, '/') AS IrcLink, b.Name AS BotName, b.InfoSpeedMax AS BotSpeed
			FROM packets p
			INNER JOIN bots b ON b.guid = p.parentguid
			INNER JOIN channels c ON c.guid = b.parentguid
			INNER JOIN servers s ON s.guid = c.parentguid
			WHERE $str;
		");

		$count = 0;
		foreach ($strings as $string)
		{
			$stmt->bindValue(':string' . $count++, '%' . $string . '%', PDO::PARAM_STR);
		}

		if($searchOptions->MaxSize > 0)
		{
			$stmt->bindValue(':maxSize', $searchOptions->MaxSize, PDO::PARAM_INT);
		}
		if($searchOptions->MinSize > 0)
		{
			$stmt->bindValue(':minSize', $searchOptions->MinSize, PDO::PARAM_INT);
		}
		if($searchOptions->LastMentioned > 0)
		{
			$stmt->bindValue(':lastMentioned', time() - $searchOptions->LastMentioned, PDO::PARAM_INT);
		}

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, 'XG\Classes\Domain\Model\PacketSearch');

		return !$result ? array() : $this->FixObjects($result);
	}

	/**
	 * @param string $search
	 * @return bool
	 */
	private function AddSearch($search)
	{
		$stmt = $this->pdo->prepare("
			INSERT INTO searches (search, lasttime) VALUES (:search, :lasttime) ON DUPLICATE KEY UPDATE count = count + 1, lasttime = :lasttime;
		");

		$stmt->bindValue(':search', $search, PDO::PARAM_STR);
		$stmt->bindValue(':lasttime', time(), PDO::PARAM_STR);
		$result = $stmt->execute();

		return $result;
	}

	/**
	 * @param int $limit
	 * @return string[]
	 */
	public function GetSearches($limit)
	{
		$stmt = $this->pdo->prepare("SELECT search, count FROM searches ORDER BY lasttime DESC, count DESC LIMIT 0, :limit;");

		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$return = array();
		foreach($result as $row)
		{
			$return[$row['search']] = $row['count'];
		}

		return $return;
	}

	/**
	 * @return Snapshot
	 */
	public function GetLastSnapshot()
	{
		$stmt = $this->pdo->prepare("SELECT * FROM snapshots ORDER BY Timestamp DESC LIMIT 0, 1;");

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS, 'XG\Classes\Domain\Model\Snapshot');

		return $result[0];
	}

	/**
	 * @param Base[] $objects
	 * @param int $sidx
	 * @param string $sord
	 * @param int $page
	 * @param int $rows
	 * @return array
	 */
	public function buildJsonArray(array $objects, $sidx, $sord, $page, $rows)
	{
		$start = ($page - 1) * $rows;
		$end = $start + $rows;

		$json_objects = array();
		if(count($objects) > 0)
		{
			$sort = new Sorter();
			$sort->Sort($objects, $sidx, $sord == "desc");

			$i = 0;
			foreach ($objects as $object)
			{
				if ($i >= $start && $i < $end)
				{
					$arr = array();
					$arr['id'] = $object->Guid;
					$arr['cell'] = $this->object2Array($object);
					$json_objects[] = $arr;
				}
				$i++;
			}
		}

		$objectsCount = sizeof($objects);
		$json = array();
		$json['page'] = $page;
		$json['total'] = ceil($objectsCount / $rows);
		$json['records'] = $objectsCount;
		$json['rows'] = $json_objects;

		return $json;
	}

	/**
	 * @param Base $object
	 * @return array
	 */
	private function object2Array($object)
	{
		$return = array();
		$reflect = new \ReflectionClass($object);
		$props = $reflect->getProperties();
		foreach ($props as $prop)
		{
			$name = $prop->getName();
			$value = $object->$name;
			$return[$name] = !is_null($value) ? $value : "";
		}
		return $return;
	}
}
