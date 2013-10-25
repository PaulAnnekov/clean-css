<?php

namespace CleanCss\Css;


interface ReaderInterface {
	function __construct($url);
	function getSelectors();
}