<?php

namespace CleanCss\Html;

class HtmlDomParser implements ReaderInterface {
	private $html;

	function __construct($url) {
		$this->html = \Sunra\PhpSimple\HtmlDomParser::file_get_html($url);
	}

	function isExists($selector) {
		return count($this->html->find($selector)) > 0;
	}

	function findCssFiles() {
		$tags = $this->html->find('link[rel="stylesheet"]');
		$cssFiles = array();
		foreach ($tags as $tag) {
			$cssFiles[]=$tag->href;
		}

		return $cssFiles;
	}
}