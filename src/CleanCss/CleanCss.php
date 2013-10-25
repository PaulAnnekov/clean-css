<?php

namespace CleanCss;

class CleanCss {
	private $unusedSelectors = array();

	function checkUrl($pageUrl) {
		$html = Html\Factory::factory('HtmlDomParser', $pageUrl);
		$cssFiles = $html->findCssFiles();
		$baseUrl = new \Net_URL2($pageUrl);

		foreach($cssFiles as $cssUrl) {
			$cssUrl = $baseUrl->resolve($cssUrl)->getURL();
			$css = Css\Factory::factory('PhpCssParser', $cssUrl);
			$blockSelectors = $css->getSelectors();

			foreach ($blockSelectors as $blockSelector) {
				$is_exists = false;
				foreach ($blockSelector as $selector) {
					if ($html->isExists($selector)) {
						$is_exists = true;
						break;
					}
				}
				if (!$is_exists) {
					$this->addUnused($cssUrl, implode(', ', $blockSelector));
				}
			}
			break;
		}

		return $this->getUnused();
	}

	private function addUnused($cssUrl, $selector) {
		if (!isset($this->unusedSelectors[$cssUrl])) {
			$this->unusedSelectors[$cssUrl] = array();
		}

		$this->unusedSelectors[$cssUrl][] = $selector;
	}

	private function getUnused() {
		return $this->unusedSelectors;
	}

	public function printUnused() {
		$unused = $this->getUnused();

		foreach ($unused as $cssUrl => $selectors) {
			echo "\n\n=====================\n";
			echo $cssUrl . "\n\n";

			foreach ($selectors as $selector) {
				echo $selector . "\n";
			}
		}
	}
}