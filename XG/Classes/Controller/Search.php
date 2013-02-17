<?php
//
//  Search.php
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

class Search extends Base
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

			case 'external':
				return $this->externalAction();

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
		$view->assign('view', 'search');
		$view->assign('title', 'Search');
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
		$searchOption->Name = isset($this->request['searchString']) ? $this->request['searchString'] : null;
		$searchOption->MaxSize = isset($this->request['searchSizeMax']) ? intval($this->request['searchSizeMax']) : null;
		if ($searchOption->MaxSize == 0)
		{
			$searchOption->MaxSize = null;
		}
		$searchOption->MinSize = isset($this->request['searchSizeMin']) ? intval($this->request['searchSizeMin']) : null;
		$searchOption->LastMentioned = isset($this->request['searchLastMentioned']) ? intval($this->request['searchLastMentioned']) : null;
		$searchOption->BotState = isset($this->request['searchBotState']) ? intval($this->request['searchBotState']) : null;
		$searchOption->Page = $this->request['page'];
		$searchOption->Start = ($this->request['page'] - 1) * $this->request['rows'];
		$searchOption->Limit = $this->request['rows'];
		$searchOption->SortBy = strtolower(substr($this->request['sidx'], 0, 1)) . substr($this->request['sidx'], 1);
		$searchOption->SortDesc = $this->request['sord'] == "desc";

		$objects = $this->service->SearchPackets($searchOption);

		$view = new View();

		$json = $this->service->buildJsonArray($objects, $searchOption);
		if(isset($this->request['callback']) && $this->request['callback'] != '')
		{
			$view->assign('callback', $this->request['callback']);
		}

		$view->assign('json', $json);
		$content = $view->loadTemplate('json', __CLASS__);

		return $content;
	}

	/**
	 * @return string
	 */
	private function externalAction ()
	{
		$searchOption = new SearchOption();
		$searchOption->Limit = 999999;
		$searchOption->Name = isset($this->request['search']) ? $this->request['search'] : "";

		$externals = array();

		$objects = $this->service->SearchPackets($searchOption);
		foreach ($objects as &$object)
		{
			$external = array();
			foreach ($object as $k => $v)
			{
				$k = strtoupper(substr($k, 0, 1)) . substr($k, 1);
				$external[$k] = $v;
			}
			$externals[] = $external;
		}

		$view = new View();
		$view->assign('json', $externals);
		$content = $view->loadTemplate('external', __CLASS__);

		return $content;
	}
}

?>
