<?php

namespace CleanCss;

/**
 * Class CleanCss performs unused CSS selectors search.
 *
 * @package CleanCss
 */
class CleanCss {
	private $htmlReader = 'HtmlDomParser';
	private $cssReader = 'CssSelectorsFinder';
	private $cssFiles = array();

	/**
	 * Searches for unused CSS selectors on whole site.
	 *
	 * @param string $siteUrl Full site url to parse.
	 * @param int $level Maximum site search depth where 1 - first level (http://example.com/page).
	 * @param int $maxPages Pages limit.
	 */
	function checkSite($siteUrl, $level = 1, $maxPages = 10) {
		$baseUrl = new \Net_URL2($siteUrl);
		$pageUrls = array($siteUrl);
		$pages = array();
		do {
			$pageUrl = array_shift($pageUrls);
			$page = Html\Factory::factory($this->htmlReader, $pageUrl);
			$pages[$pageUrl] = $page;

			// Don't search new urls on current page if we have reached the limit.
			if (count($pageUrls) + count($pages) >= $maxPages)
				continue;

			$newPageUrls = $page->findPageUrls();
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

	/**
	 * Searches for unused CSS selectors on urls list.
	 *
	 * @param array $pageUrls Pages urls list.
	 */
	function checkUrls($pageUrls) {
		$pages = array();
		foreach ($pageUrls as $pageUrl) {
			$pages[$pageUrl] = Html\Factory::factory($this->htmlReader, $pageUrl);
		}

		$this->pagesSearch($pages);
	}

	/**
	 * Searches and prints selectors on passed pages urls.
	 *
	 * @param array $pages Pages list where each entry has the following format:
	 * <code>"page url" => Html\ReaderInterface instance object</code>
	 */
	private function pagesSearch($pages) {
		$this->cssFiles = array();

		foreach ($pages as $pageUrl => $page) {
			$baseUrl = new \Net_URL2($pageUrl);
			/** @var Html\ReaderInterface $page */
			$pageCssFiles = $page->findCssFiles();

			foreach($pageCssFiles as $cssUrl) {
				$cssUrl = $baseUrl->resolve($cssUrl)->getURL();
				$selectorData = $this->getCssSelectors($cssUrl);

				foreach ($selectorData as $selectorsGroup => $selectors) {
					$is_exists = false;
					foreach ($selectors as $selector) {
						if ($page->isExists($selector)) {
							$is_exists = true;
							break;
						}
					}
					if ($is_exists) {
						unset($this->cssFiles[$cssUrl][$selectorsGroup]);
					}
				}
			}
		}
	}

	/**
	 * Gets CSS selectors from passed file url.
	 *
	 * @param string $cssUrl CSS file url.
	 * @return array Selectors list where each entry has the following format:
	 * <code>"css file url" => array(".selector1", "#selector2", ...)</code>
	 */
	private function getCssSelectors($cssUrl) {
		if (!isset($this->cssFiles[$cssUrl])) {
			$css = Css\Factory::factory($this->cssReader, $cssUrl);
			$selectorData = $css->getSelectors();
			$this->cssFiles[$cssUrl] = $selectorData;
		} else {
			$selectorData = $this->cssFiles[$cssUrl];
		}

		return $selectorData;
	}

	/**
	 * Gets url level.
	 *
	 * @param string $url Url.
	 * @return int Url level where 1 - first level (http://example.com/page).
	 */
	private function getUrlLevel($url) {
		$url = new \Net_URL2($url);
		$path = trim($url->getPath(), '/');

		return count(explode('/', $path));
	}

	/**
	 * Prints unused css selectors.
	 */
	public function printUnused() {
		foreach ($this->cssFiles as $cssUrl => $selectorData) {
			echo "\n\n=====================\n";
			echo $cssUrl . "\n";

			if (empty($selectorData)) {
				echo "\n";
				continue;
			}

			foreach ($selectorData as $selectorsGroup => $selectors) {
				echo "\n" . $selectorsGroup;
			}
		}
	}
}