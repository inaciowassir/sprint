<?php

use \sprint\app\helpers\Helpers;

function route($route = "")
{
	$route = ltrim($route, "/");
	$route = rtrim($route, "/");
	
	return Helpers::root().$route;
}

function asset($asset)
{
	$asset = ltrim($asset, "/");
	$asset = rtrim($asset, "/");
	
	return Helpers::asset().$asset;
}

function upload($source)
{
	$source = ltrim($source, "/");
	$source = rtrim($source, "/");
	
	return Helpers::upload().$source;
}

function image($source, $w = 150, $h = 150, $a = "c")
{
	$source = ltrim($source, "/");
	$source = rtrim($source, "/");
	
	return route("timthumb.php?src=") . upload($source) . "&w={$w}&h={$h}&a={$a}";
}

function excerpt($string, $words = '200', $ending = '(...)')
{
	return Helpers::excerpt($string, $words, $ending);
}

function timeAgo($time, $prefixText = "cerca de")
{
	return Helpers::getTimeAgo($time, $prefixText);
}

function number($numbers, $decimal)
{
	return number_format($numbers, $decimal);
}