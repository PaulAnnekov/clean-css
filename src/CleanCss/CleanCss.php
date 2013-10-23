<?php

namespace CleanCss;

use \Sunra\PhpSimple\HtmlDomParser;
use \Sabberworm\CSS\Parser;

class CleanCss {
	function checkUrl($pageUrl) {
		// Create DOM from URL or file
		$html = HtmlDomParser::file_get_html($pageUrl);
		$baseUrl = new \Net_URL2($pageUrl);

		foreach($html->find('link[type="text/css"]') as $element) {
			$cssUrl = $baseUrl->resolve($element->href);
			$cssContent = file_get_contents($cssUrl);
			$css = new Parser($cssContent);
			$css->parse();
		}
	}
}