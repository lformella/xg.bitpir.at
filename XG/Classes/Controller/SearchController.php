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

class XG_Classes_Controller_SearchController extends XG_Classes_Controller_AbstractController
{
	/**
	 * @param array $request
	 * @param XG_Classes_Domain_Service $service
	 */
	public function __construct (array $request, XG_Classes_Domain_Service $service)
	{
		parent::__construct($request, $service);
		$this->name = "Search";
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
		$view = new XG_Classes_View();
		$content = $view->loadTemplate('index', $this->name);

		$view = new XG_Classes_View();
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
		$start = ($this->request['page'] - 1) * $this->request['rows'];
		$end = $start + $this->request['rows'];

		$view = new XG_Classes_View();

		$sort = new XG_Classes_Domain_Sorter();

		$objects = array();
		$json_objects = array();

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

		$objectsCount = sizeof($objects);
		$json = array();
		$json['page'] = $this->request['page'];
		$json['total'] = ceil($objectsCount / $this->request['rows']);
		$json['records'] = $objectsCount;
		$json['rows'] = $json_objects;

		$view->assign('json', $json);
		$content = $view->loadTemplate('json', $this->name);

		return $content;
	}
}

?>
