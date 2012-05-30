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

namespace XG\Classes;

use PDO;
use XG\Classes\Controller\Base;
use XG\Classes\Controller\Index;
use XG\Classes\Controller\Network;
use XG\Classes\Controller\Search;
use XG\Classes\Domain\Service;

class Dispatcher
{
	/** @var string[] */
	private $request = array();

	/**
	 * @param array $request
	 */
	public function __construct (array $request)
	{
		$this->request = $request;
	}

	/**
	 * @return string
	 */
	public function display ()
	{
		$show = isset($this->request['show']) && !empty($this->request['show']) ? $this->request['show'] : 'index';

		/** @var $controller Base */
		$controller = null;

		$db = new PDO('mysql:host=localhost;dbname=xg', 'xg', 'xg');
		$service = new Service($db);

		switch ($show)
		{
			case 'search':
				$controller = new Search($this->request, $service);
				break;

			case 'network':
				$controller = new  Network($this->request, $service);
				break;

			case 'index':
			default:
				$controller = new  Index($this->request, $service);
				break;
		}

		return $controller->display();
	}
}
