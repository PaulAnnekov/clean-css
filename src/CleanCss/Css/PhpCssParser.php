<?php

namespace CleanCss\Css;

use \Sabberworm\CSS\Parser;
use \Sabberworm\CSS\RuleSet\DeclarationBlock;
use \Sabberworm\CSS\Property\Selector;

class PhpCssParser implements ReaderInterface {
	private $css;

	function __construct($url) {
		$cssContent = file_get_contents($url);
		$parser = new Parser($cssContent);
		$this->css = $parser->parse();
	}

	function getSelectors() {
		$selectors = array();
		$blocks = $this->css->getAllDeclarationBlocks();
		foreach ($blocks as $block) {
			/** @var DeclarationBlock $block */
			$blockSelectors = array();
			foreach ($block->getSelectors() as $selector) {
				/** @var Selector $selector */
				$blockSelectors[] = $selector->getSelector();
			}
			$selectors[] = $blockSelectors;
		}

		return $selectors;
	}
}