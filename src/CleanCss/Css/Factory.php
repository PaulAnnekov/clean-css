<?php

namespace CleanCss\Css;


class Factory {
	/**
	 * @param $class
	 * @param $url
	 * @return ReaderInterface
	 */
	public static function factory($class, $url) {
		$class = '\\' . __NAMESPACE__ . '\\' . $class;

		return new $class($url);
	}
}