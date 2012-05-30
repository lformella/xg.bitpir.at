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

namespace XG\Classes\Domain;

use XG\Classes\Controller\Base;

class Sorter
{
	private $variable;
	private $reverse;

	/**
	 * @param  Base[] $array
	 * @param  string $variable
	 * @param  bool   $reverse
	 *
	 * @return void
	 */
	public function Sort (&$array, $variable, $reverse = false)
	{
		$this->variable = $variable;
		$this->reverse = $reverse;
		uasort($array, array($this, 'CompareObjectsBy'));
	}

	/**
	 * @param Base $obj1
	 * @param Base $obj2
	 *
	 * @return int
	 */
	private function CompareObjectsBy ($obj1, $obj2)
	{
		$var = $this->variable;
		$reverse = $this->reverse ? 1 : -1;

		if ($obj1->$var == $obj2->$var)
		{
			return 0;
		}
		return ($obj1->$var < $obj2->$var ? +1 : -1) * $reverse;
	}
}
