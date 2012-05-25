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

abstract class XG_Classes_Controller_AbstractController
{
	/** @var array */
	protected $request = array();
	/** @var string */
	protected $name = '';
	/** @var XG_Classes_Domain_Service */
	protected $service = null;

	/**
	 * @param array $request
	 * @param XG_Classes_Domain_Service $service
	 */
	public function __construct (array $request, XG_Classes_Domain_Service $service)
	{
		$this->request = $request;
		$this->service = $service;
	}

	/**
	 * @return string
	 */
	public abstract function display ();
}
