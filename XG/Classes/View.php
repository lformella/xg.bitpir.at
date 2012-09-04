<?php
//
//  View.php
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

namespace XG\Classes;

class View
{
	/** @var mixed[] */
	private $variables = array();

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function assign ($key, $value)
	{
		$this->variables[$key] = $value;
	}

	/**
	 * @param string $template
	 * @param string $controller
	 * @return string
	 */
	public function loadTemplate ($template, $controller = '')
	{
		if($controller != '')
		{
			$names = explode('\\', $controller);
			$controller = '/' . array_pop($names);
		}
		$file = __DIR__ . '/../Templates' . $controller . '/' . $template . '.php';

		if (file_exists($file))
		{
			ob_start();

			foreach ($this->variables as $key => $value)
			{
				$$key = $value;
			}
			include $file;
			$output = ob_get_contents();

			ob_end_clean();

			return $output;
		}
		else
		{
			return 'could not find template ' . $template . ' for controller ' . $controller;
		}
	}
}
