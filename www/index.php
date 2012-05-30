<?php

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
