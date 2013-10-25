<?php

namespace CleanCss\Html;


interface ReaderInterface {
	function __construct($url);
	function isExists($selector);
	function findCssFiles();
}