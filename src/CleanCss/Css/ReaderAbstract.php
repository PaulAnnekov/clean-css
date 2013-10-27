<?php

namespace CleanCss\Css;


abstract class ReaderAbstract {
	abstract function __construct($url);
	abstract function _getSelectors();

	function getSelectors() {
		$selectors = $this->_getSelectors();

		// Remove pseudo-classes and pseudo-elements.
		foreach ($selectors as &$blockSelectors) {
			foreach ($blockSelectors as &$selector) {
				$selector = preg_replace('/:[^ #.,]+/i', '', $selector);
			}
		}

		return $selectors;
	}
}