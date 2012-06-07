<?php

$echo = json_encode($json);

if(isset($callback) && $callback != '')
{
	$echo = $callback.'('.$echo.');';
}

echo $echo;
