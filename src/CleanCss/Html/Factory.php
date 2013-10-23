<?php

namespace CleanCss\Html;


class Factory {
	public static function factory($class) {
		return new $class;
	}
}