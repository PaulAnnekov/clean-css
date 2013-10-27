<?php

namespace CleanCss\Html;

interface ReaderInterface {
	function __construct($url);

	/**
	 * Checks tag existence in DOM tree by its selector.
	 *
	 * @param string $selector Tag selector.
	 * @return bool <tt>true</tt> if exists, <tt>false</tt> otherwise.
	 */
	function isExists($selector);

	/**
	 * Gets CSS files urls on current page.
	 *
	 * @return array List of CSS files urls.
	 */
	function findCssFiles();

	/**
	 * Gets page urls on current page.
	 *
	 * @return array List of page urls.
	 */
	function findPageUrls();
}