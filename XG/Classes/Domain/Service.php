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

namespace XG\Classes\Domain;

use PDO;
use XG\Classes\Domain\Model\Base;
use XG\Classes\Domain\Model\Bot;
use XG\Classes\Domain\Model\Channel;
use XG\Classes\Domain\Model\Packet;
use XG\Classes\Domain\Model\PacketSearch;
use XG\Classes\Domain\Model\Server;

class Service
{
	/** @var PDO */
	protected $pdo;

	/**
	 * @param PDO $pdo
	 */
	public function __construct (PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * @param  string $guid
	 *
	 * @return Server
	 */
	public function GetServer ($guid)
	{
		$stmt = $this->pdo->prepare("
			SELECT *, CONCAT('irc://', name, ':', port, '/') AS IrcLink
			FROM server
			WHERE guid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Server');
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_CLASS);

		return !$result ? null : $result;
	}

	/**
	 * @return Server[]
	 */
	public function GetServers ()
	{
		$stmt = $this->pdo->prepare("
			SELECT *, CONCAT('irc://', name, ':', port, '/') AS IrcLink
			FROM server;
		");
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Server');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
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
			FROM channel c
			INNER JOIN server s ON s.guid = c.parentguid
			WHERE c.parentguid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Channel');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
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
			FROM bot b
			INNER JOIN channel c ON c.guid = b.parentguid
			INNER JOIN server s ON s.guid = c.parentguid
			WHERE b.parentguid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Bot');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
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
			FROM packet p
			INNER JOIN bot b ON b.guid = p.parentguid
			INNER JOIN channel c ON c.guid = b.parentguid
			INNER JOIN server s ON s.guid = c.parentguid
			WHERE p.parentguid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Packet');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}

	/**
	 * @param  string[] $strings
	 *
	 * @return PacketSearch[]
	 */
	public function SearchPackets ($strings)
	{
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

		$stmt = $this->pdo->prepare("
			SELECT p.*, CONCAT('xdcc://', s.name, '/', s.name, '/', c.name, '/', b.name, '/#', p.id, '/', p.name, '/') AS IrcLink, b.Name AS BotName, b.InfoSpeedMax AS BotSpeed
			FROM packet p
			INNER JOIN bot b ON b.guid = p.parentguid
			INNER JOIN channel c ON c.guid = b.parentguid
			INNER JOIN server s ON s.guid = c.parentguid
			WHERE $str;
		");

		$count = 0;
		foreach ($strings as $string)
		{
			$stmt->bindValue(':string' . $count++, '%' . $string . '%', PDO::PARAM_STR);
		}
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'PacketSearch');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}
}
