<?php

function autoLoad($className)
{
	$file = __DIR__ . '/../' . str_replace('_', '/', $className) . '.php';
	if (file_exists($file))
	{
		require_once($file);
		return true;
	}
	return false;
}

spl_autoload_register('autoLoad');

$request = array_merge($_GET, $_POST);
$dispatcher = new XG_Classes_Dispatcher($request);
echo $dispatcher->display();
