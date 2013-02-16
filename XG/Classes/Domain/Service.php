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

use \Elastica\Client;
use XG\Classes\Domain\Model\SearchOption;

class Service
{
	/**
	 * @param \Elastica\Client $client
	 *
	 * @return Service
	 */
	public function __construct (\Elastica\Client $client)
	{
		$this->client = $client;
		$this->index = $client->getIndex('xg');
	}

	/**
	 * @param string       $type
	 * @param SearchOption $searchOptions
	 *
	 * @return array
	 */
	private function GetObjects ($type, SearchOption $searchOptions)
	{
		$objects = array();

		$query = new \Elastica\Query();
		$query->setFrom($searchOptions->Start);
		$query->setLimit($searchOptions->Limit);

		if (!is_null($searchOptions->Name))
		{
			$queryBool = new \Elastica\Query\Bool();

			$strings = explode(' ', $searchOptions->Name);
			foreach ($strings as $string)
			{
				$string = trim($string);
				if ($string != "")
				{
					$currentQuery = new \Elastica\Query\Field();
					$currentQuery->setQueryString("*" . $string . "*");
					$currentQuery->setField("name");

					$queryBool->addMust($currentQuery);
				}
			}

			if($searchOptions->LastMentioned > 0)
			{
				$date = new \DateTime();
				$date->sub(new \DateInterval("PT" . $searchOptions->LastMentioned . "S"));
				$currentRange = new \Elastica\Query\Range();
				$currentRange->addField('lastMentioned', array(
					'gte' => $date->format("c"),
				));
				$queryBool->addMust($currentRange);
			}

			switch($searchOptions->BotState)
			{
				case 0:
					break;
				case 1:
					$currentQuery = new \Elastica\Query\Term();
					$currentQuery->setTerm("botHasFreeSlots", true);
					$queryBool->addMust($currentQuery);
					break;
					break;
				case 2:
					$currentQuery = new \Elastica\Query\Term();
					$currentQuery->setTerm("botHasFreeQueue", true);
					$queryBool->addMust($currentQuery);
					break;
				case 3:
					$currentQuery = new \Elastica\Query\Term();
					$currentQuery->setTerm("botConnected", true);
					$queryBool->addMust($currentQuery);
					break;
			}

			$mainQuery = new \Elastica\Query\Filtered(
				$queryBool,
				new \Elastica\Filter\Range("size", array(
					"from" => $searchOptions->MinSize,
					"to" => $searchOptions->MaxSize,
				))
			);

			$query->setQuery($mainQuery);
		}

		if (!is_null($searchOptions->ParentGuid))
		{
			$queryString = new \Elastica\Query\QueryString();
			$queryString->setQuery($searchOptions->ParentGuid);
			$queryString->setFields(array("parentGuid"));

			$query->setQuery($queryString);
		}

		if ($searchOptions->SortBy == "name")
		{
			$searchOptions->SortBy = "name.raw";
		}
		$query->setSort(array($searchOptions->SortBy => $searchOptions->SortDesc ? "desc" : "asc"));

		$resultSet = $this->index->getType($type)->search($query);
		$searchOptions->ResultCount = $resultSet->getTotalHits();

		/** @var $results \Elastica\Result[] */
		$results = $resultSet->getResults();
		foreach ($results as $result)
		{
			$objects[] = $result->getSource();
		}

		return $objects;
	}

	/**
	 * @param SearchOption $searchOption
	 *
	 * @return array
	 */
	public function GetServers (SearchOption $searchOption)
	{
		$objects = $this->GetObjects("server", $searchOption);
		return $objects;
	}

	/**
	 * @param SearchOption $searchOption
	 *
	 * @return array
	 */
	public function GetChannelsFromServer (SearchOption $searchOption)
	{
		$objects = $this->GetObjects("channel", $searchOption);
		return $objects;
	}

	/**
	 * @param SearchOption $searchOption
	 *
	 * @return array
	 */
	public function GetBotsFromChannel (SearchOption $searchOption)
	{
		$objects = $this->GetObjects("bot", $searchOption);
		return $objects;
	}

	/**
	 * @param SearchOption $searchOption
	 *
	 * @return array
	 */
	public function GetPacketsFromBot (SearchOption $searchOption)
	{
		$objects = $this->GetObjects("packet", $searchOption);
		return $objects;
	}

	/**
	 * @param SearchOption $searchOption
	 *
	 * @return array
	 */
	public function SearchPackets (SearchOption $searchOption)
	{
		$this->AddSearch($searchOption->Name);

		$objects = $this->GetObjects("packet", $searchOption);
		return $objects;
	}

	/**
	 * @param string $search
	 *
	 * @return bool
	 */
	private function AddSearch($search)
	{
		$count = 1;

		try
		{
			$doc = $this->index->getType("search")->getDocument($search);
			$data = $doc->getData();
			$count += $data["count"];
		}
		catch (\Elastica\Exception\NotFoundException $e) {}

		$doc = new \Elastica\Document($search, array("search" => $search, "timestamp" => time(), "count" => $count));
		$this->index->getType("search")->addDocument($doc);
		$this->index->getType("search")->getIndex()->refresh();
	}

	/**
	 * @param int $limit
	 *
	 * @return string[]
	 */
	public function GetSearches($limit)
	{
		$searches = array();

		$query = new \Elastica\Query();
		$query->setFrom(0);
		$query->setLimit($limit);

		$query->setSort(array("timestamp" => "desc"));

		$resultSet = $this->index->getType("search")->search($query);

		/** @var $results \Elastica\Result[] */
		$results = $resultSet->getResults();
		foreach ($results as $result)
		{
			$search = $result->getSource();
			$searches[$search["search"]] = $search["count"];
		}

		return $searches;
	}

	/**
	 * @param  SearchOption $searchOptions
	 * @return int[]
	 */
	public function GetSnapshots($searchOptions)
	{
		$objects = $this->GetObjects("snapshot", $searchOptions);
		return $objects;
	}

	/**
	 * @param array[]      $objects
	 * @param SearchOption $searchOption
	 *
	 * @return array
	 */
	public function buildJsonArray(array $objects, SearchOption $searchOption)
	{
		$json_objects = array();
		foreach ($objects as $object)
		{
			$arr = array();
			$arr['id'] = $object["guid"];
			$arr['cell'] = $object;
			$json_objects[] = $arr;
		}

		$json = array();
		$json['page'] = $searchOption->Page;
		$json['total'] = ceil($searchOption->ResultCount / $searchOption->Limit);
		$json['records'] = $searchOption->ResultCount;
		$json['rows'] = $json_objects;

		return $json;
	}
}
