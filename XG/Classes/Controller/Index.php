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

class XG_Classes_Controller_Index extends XG_Classes_Controller_Abstract
{
	/**
	 * @param array $request
	 * @param XG_Classes_Domain_Service $service
	 */
	public function __construct (array $request, XG_Classes_Domain_Service $service)
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

		$view = new XG_Classes_View();
		$view->assign('count', $count);
		$content = $view->loadTemplate('index', __CLASS__);

		$view = new XG_Classes_View();
		$view->assign('title', '');
		$view->assign('content', $content);
		$content = $view->loadTemplate('page');

		return $content;
	}
}
