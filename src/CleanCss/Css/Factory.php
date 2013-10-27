<?php

namespace CleanCss\Css;


class Factory {
	/**
	 * Gets ReaderAbstract instance object.
	 *
	 * @param string $class ReaderAbstract instance name.
	 * @param string $url CSS file url to parse.
	 * @return ReaderAbstract Instance object.
	 */
	public static function factory($class, $url) {
		$class = '\\' . __NAMESPACE__ . '\\' . $class;

		return new $class($url);
	}
}