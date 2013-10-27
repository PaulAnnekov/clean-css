<?php

require 'vendor/autoload.php';

if (count($argv) < 3) {
	echo 'Usage: clean-css.php [OPTION]... [URL]...

  -p	Search on provided pages list
  -s	Site search
  -l	Site search depth where 1 - first level (http://example.com/page). Default - 1.
  -m	The maximum number of pages to be scanned on site search. Default - 10.

  Examples:
  clean-css.php -p http://habrahabr.ru/ http://habrahabr.ru/users/thesteelrat/	Takes search on two pages.
  clean-css.php -s -l 3 -m 20 http://habrahabr.ru/	Takes search on site with a maximum depth of 10 levels and no more then 20 pages.
';

	return;
}

$options = getopt('l:m:ps');

$cleanCss = new \CleanCss\CleanCss();
if (isset($options['s'])) {
	$level = 1;
	if (isset($options['l'])) {
		$level = $options['l'];
	}
	$maxPages = 1;
	if (isset($options['m'])) {
		$maxPages = $options['m'];
	}

	$cleanCss->checkSite(end($argv), $level, $maxPages);
} else {
	$cleanCss->checkUrls(array_splice($argv, 2));
}

$cleanCss->printUnused();