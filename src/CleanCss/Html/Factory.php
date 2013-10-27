<?php

namespace CleanCss\Html;


class Factory {
	/**
	 * Gets ReaderInterface instance object.
	 *
	 * @param string $class ReaderInterface instance name.
	 * @param string $url Page url to parse.
	 * @return ReaderInterface Instance object.
	 */
	public static function factory($class, $url) {
		$class = '\\' . __NAMESPACE__ . '\\' . $class;

		return new $class($url);
	}
}