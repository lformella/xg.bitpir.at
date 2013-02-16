<?php
//
//  Index.php
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
		$searchOption = new SearchOption();
		$searchOption->Page = 0;
		$searchOption->Start = 0;
		$searchOption->Limit = 1;
		$searchOption->SortBy = "timestamp";
		$searchOption->SortDesc = true;

		$snapshot = $this->service->GetSnapshots($searchOption);
		$snapshot = $snapshot[0];
		$searches = $this->service->GetSearches(50);

		$view = new View();
		$view->assign('snapshot', $snapshot);
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
