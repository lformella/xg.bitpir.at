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

abstract class Base
{
	/** @var array */
	protected $request = array();
	/** @var Service */
	protected $service = null;

	/**
	 * @param array $request
	 * @param Service $service
	 */
	public function __construct (array $request, Service $service)
	{
		$this->request = $request;
		$this->service = $service;
	}

	/**
	 * @return string
	 */
	public abstract function display ();
}
