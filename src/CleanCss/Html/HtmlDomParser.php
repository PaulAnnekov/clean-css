<?php

namespace CleanCss\Html;

class HtmlDomParser implements FinderInterface {
	private $html;

	function __construct() {
		$this->html = new \Sunra\PhpSimple\HtmlDomParser();
	}

	function find($selector) {
		return $this->html->find($selector);
	}
}