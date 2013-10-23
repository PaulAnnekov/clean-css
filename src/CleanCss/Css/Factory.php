<?php

namespace CleanCss\Css;


class Factory {
	public static function factory($class) {
		return new $class;
	}
}