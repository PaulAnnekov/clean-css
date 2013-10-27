<?php

namespace CleanCss\Css;

use \Sabberworm\CSS\Parser;
use \Sabberworm\CSS\RuleSet\DeclarationBlock;
use \Sabberworm\CSS\Property\Selector;

/**
 * Class PhpCssParser reads CSS file using very simple algorithm to extract only selectors.
 *
 * @package CleanCss\CSS
 */
class CssSelectorsFinder extends ReaderAbstract {
	private $cssContent;
	private $currentOffset=0;
	private $selectors = array();


	function __construct($url) {
		$this->cssContent = file_get_contents($url);
		$this->parse();
	}

	private function parse() {
		$selector = '';
		while($this->currentOffset < strlen($this->cssContent)) {
			// Remove whitespaces only before selector appeared.
			if (!strlen($selector) && preg_match('/\\s/isSu', $this->cssContent[$this->currentOffset]) == 1) {
				$this->currentOffset++;
				continue;
			}

			switch ($this->cssContent[$this->currentOffset]) {
				case '@':
					$this->consumeAtRule();
					break;
				case '/':
					$this->consumeUntil('*/');
					break;
				case '{':
					// May be empty for such declaration blocks: "{}". Was first seen on habrahabr.ru in all.css
					// stylesheet.
					if (strlen($selector)) {
						// Remove whitespaces right of the selector.
						$this->selectors[] = rtrim($selector);
						$selector = '';
					}
					$this->consumeUntil('}');
					break;
				// May be left after @media rule removed.
				case '}':
				// Trash.
				case "\n":
				case "\r":
					break;
				default:
					$selector .= substr($this->cssContent, $this->currentOffset, 1);
					break;
			}

			$this->currentOffset++;
		}
	}

	private function consumeAtRule() {
		$pos = strpos($this->cssContent, ' ', $this->currentOffset);
		$atRule = substr($this->cssContent, $this->currentOffset + 1, $pos - $this->currentOffset - 1);
		$this->currentOffset = $pos;
		switch ($atRule) {
			case  'charset':
			case  'import':
				$this->consumeUntil(';');
				break;
			case  'media':
				$this->consumeUntil('{');
				break;
		}
	}

	private function consumeUntil($word) {
		$pos = strpos($this->cssContent, $word, $this->currentOffset);
		if ($pos !== false) {
			$this->currentOffset = ++$pos;
		}

		return $pos !== false;
	}

	function _getSelectors() {
		$selectors = array();
		foreach ($this->selectors as $selector) {
			$rawSelectors = explode(',', $selector);
			$blockSelectors = array_map('trim', $rawSelectors);
			$selectors[$selector] = $blockSelectors;
		}

		return $selectors;
	}
}