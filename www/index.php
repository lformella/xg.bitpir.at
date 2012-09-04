<?php
//
//  index.php
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

spl_autoload_register(
	function ($className)
	{
		$file = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';
		if (file_exists($file))
		{
			require_once($file);
			return true;
		}
		return false;
	}
);

use XG\Classes\Dispatcher;

$request = array_merge($_GET, $_POST);
$dispatcher = new Dispatcher($request);
echo $dispatcher->display();
