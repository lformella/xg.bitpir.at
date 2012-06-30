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
use XG\Classes\View;

class Index extends Base
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
		return $this->indexAction();
	}

	/**
	 * @return string
	 */
	private function indexAction ()
	{
		$count = array(0, 0, 0, 0, 0);

		$servers = $this->service->GetServers();
		foreach ($servers as $server)
		{
			if ($server->Connected)
			{
				$count[0]++;
			}
			else
			{
				$count[1]++;
			}

			$count[2] += $server->ChannelCount;
			$count[3] += $server->BotCount;
			$count[4] += $server->PacketCount;
		}

		$searches = $this->service->GetSearches(50);

		$view = new View();
		$view->assign('count', $count);
		$view->assign('searches', $searches);
		$content = $view->loadTemplate('index', __CLASS__);

		$view = new View();
		$view->assign('view', 'index');
		$view->assign('title', '');
		$view->assign('content', $content);
		$content = $view->loadTemplate('page');

		return $content;
	}
}
