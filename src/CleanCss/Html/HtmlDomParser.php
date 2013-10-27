<?php

namespace CleanCss\Html;

/**
 * Class HtmlDomParser reads HTML file using PHP Simple HTML DOM Parser library.
 *
 * @package CleanCss\Html
 * @see http://sourceforge.net/projects/simplehtmldom/
 */
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

	function findPageUrls() {
		$tags = $this->html->find('a');
		$pageUrls = array();
		foreach ($tags as $tag) {
			$pageUrls[]=$tag->href;
		}

		return $pageUrls;
	}
}