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

use XG\Classes\Domain\Model\SearchOption;
use XG\Classes\Domain\Service;
use XG\Classes\Domain\Sorter;
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
		$objects = array();

		$searchOption = new SearchOption();
		$searchOption->Name = isset($this->request['searchString']) ? $this->request['searchString'] : "";
		$searchOption->MaxSize = isset($this->request['searchSizeMax']) ? intval($this->request['searchSizeMax']) : 0;
		$searchOption->MinSize = isset($this->request['searchSizeMin']) ? intval($this->request['searchSizeMin']) : 0;
		$searchOption->LastMentioned = isset($this->request['searchLastMentioned']) ? intval($this->request['searchLastMentioned']) : 0;
		$searchOption->BotState = isset($this->request['searchBotState']) ? intval($this->request['searchBotState']) : 0;

		if (strlen($searchOption->Name) >= 3)
		{
			$objects = $this->service->SearchPackets($searchOption);
		}

		$view = new View();

		$json = $this->service->buildJsonArray($objects, $this->request['sidx'], $this->request['sord'], $this->request['page'], $this->request['rows']);
		if(isset($this->request['callback']) && $this->request['callback'] != '')
		{
			$view->assign('callback', $this->request['callback']);
		}

		$view->assign('json', $json);
		$content = $view->loadTemplate('json', __CLASS__);

		return $content;
	}
}

?>
