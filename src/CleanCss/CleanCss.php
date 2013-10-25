<?php

namespace CleanCss;

class CleanCss {
	function checkSite($siteUrl, $level = 1, $maxPages = 10) {
		$baseUrl = new \Net_URL2($siteUrl);
		$pageUrls = array($siteUrl);
		$pages = array();
		do {
			$pageUrl = array_shift($pageUrls);
			$page = Html\Factory::factory('HtmlDomParser', $pageUrl);
			$pages[$pageUrl] = $page;
			$newPageUrls = $page->findPageUrls();
			// TODO: unnecessary search because $maxPages is not checked.
			foreach ($newPageUrls as $pageUrl) {
				// Normalize url (make it absolute and remove # part).
				$pageUrl = $baseUrl->resolve($pageUrl)->setFragment(false)->getURL();
				// Discard higher depth level and already parsed pages.
				if ($this->getUrlLevel($pageUrl) <= $level && !isset($pages[$pageUrl])) {
					$pageUrls[] = $pageUrl;
				}
			}
		} while (!empty($pageUrls) && count($pages) < $maxPages);

		$this->pagesSearch($pages);
	}

	function checkUrls($pageUrls) {
		$pages = array();
		foreach ($pageUrls as $pageUrl) {
			$pages[$pageUrl] = Html\Factory::factory('HtmlDomParser', $pageUrl);
		}

		$this->pagesSearch($pages);
	}

	private function pagesSearch($pages) {
		$cssFiles = array();
		foreach ($pages as $pageUrl => $page) {
			$baseUrl = new \Net_URL2($pageUrl);
			/** @var Html\ReaderInterface $page */
			$pageCssFiles = $page->findCssFiles();

			foreach($pageCssFiles as $cssUrl) {
				$cssUrl = $baseUrl->resolve($cssUrl)->getURL();
				if (!isset($cssFiles[$cssUrl])) {
					$css = Css\Factory::factory('PhpCssParser', $cssUrl);
					$selectorData = $css->getSelectors();
					$cssFiles[$cssUrl] = $selectorData;
				} else {
					$selectorData = $cssFiles[$cssUrl];
				}

				foreach ($selectorData as $selectorsGroup => $selectors) {
					$is_exists = false;
					foreach ($selectors as $selector) {
						if ($page->isExists($selector)) {
							$is_exists = true;
							break;
						}
					}
					if ($is_exists) {
						unset($cssFiles[$cssUrl][$selectorsGroup]);
					}
				}
			}
		}

		$this->printUnused($cssFiles);
	}

	private function getUrlLevel($url) {
		$url = new \Net_URL2($url);
		$path = trim($url->getPath(), '/');

		return count(explode('/', $path));
	}

	public function printUnused($unusedSelectors) {
		foreach ($unusedSelectors as $cssUrl => $selectorData) {
			echo "\n\n=====================\n";
			echo $cssUrl . "\n\n";

			foreach ($selectorData as $selectorsGroup => $selectors) {
				echo $selectorsGroup . "\n";
			}
		}
	}
}