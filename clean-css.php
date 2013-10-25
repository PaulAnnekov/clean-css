<?php

require 'vendor/autoload.php';

if (count($argv) < 2) {
	throw new Exception('You must specify page urls to scan');
}

$cleanCss = new \CleanCss\CleanCss();
$cleanCss->checkUrl($argv[1]);
$cleanCss->printUnused();