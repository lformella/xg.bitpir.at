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

class XG_Classes_Domain_Service
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
	 * @return XG_Domain_Model_Object
	 */
	public function GetServer ($guid)
	{
		$stmt = $this->pdo->prepare("
			SELECT *, CONCAT('irc://', name, ':', port, '/') AS IrcLink
			FROM server
			WHERE guid = :guid;
		");
		$stmt->bindValue(':guid', $guid, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'XG_Domain_Model_ServerObject');
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_CLASS);

		return !$result ? null : $result;
	}

	/**
	 * @return XG_Domain_Model_Server[]
	 */
	public function GetServers ()
	{
		$stmt = $this->pdo->prepare("
			SELECT *, CONCAT('irc://', name, ':', port, '/') AS IrcLink
			FROM server;
		");
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'XG_Domain_Model_ServerObject');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}

	/**
	 * @param  string $guid
	 *
	 * @return XG_Domain_Model_Channel[]
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
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'XG_Domain_Model_ChannelObject');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}

	/**
	 * @param  string $guid
	 *
	 * @return XG_Domain_Model_Bot[]
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
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'XG_Domain_Model_BotObject');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}

	/**
	 * @param  string $guid
	 *
	 * @return XG_Domain_Model_Packet[]
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
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'XG_Domain_Model_PacketObject');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}

	/**
	 * @param  string[] $strings
	 *
	 * @return XG_Domain_Model_PacketSearch[]
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
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'XG_Domain_Model_PacketSearchObject');
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_CLASS);

		return !$result ? array() : $result;
	}
}
