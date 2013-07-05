<?php
//
//  Network.php
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

namespace XG\Classes\Controller;

use XG\Classes\Domain\Model\SearchOption;
use XG\Classes\Domain\Service;
use XG\Classes\View;

class Network extends Base
{
	/**
	 * @param array   $request
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
		$view->assign('view', 'network');
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
		$searchOption = new SearchOption();
		$searchOption->ParentGuid = $this->request['guid'];
		$searchOption->Page = $this->request['page'];
		$searchOption->Start = ($this->request['page'] - 1) * $this->request['rows'];
		$searchOption->Limit = $this->request['rows'];
		$searchOption->SortBy = strtolower(substr($this->request['sidx'], 0, 1)) . substr($this->request['sidx'], 1);
		$searchOption->SortDesc = $this->request['sord'] == 'desc';

		$objects = array();

		switch ($this->request['do'])
		{
			case 'get_servers' :
				$objects = $this->service->GetServers($searchOption);
				break;

			case 'get_channels_from_server' :
				$objects = $this->service->GetChannelsFromServer($searchOption);
				break;

			case 'get_bots_from_channel' :
				$objects = $this->service->GetBotsFromChannel($searchOption);
				break;

			case 'get_packets_from_bot' :
				$objects = $this->service->GetPacketsFromBot($searchOption);
				break;
		}

		$json = $this->service->buildJsonArray($objects, $searchOption);

		$view = new View();
		$view->assign('json', $json);
		$content = $view->loadTemplate('json', __CLASS__);

		return $content;
	}
}
