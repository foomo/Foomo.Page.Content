<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Page\Content;

use DOMDocument as Doc;
use DOMElement as El;
use Foomo\Page\Content;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Renderer
{
	public static function renderURLForMedium(Node $node, $srcAttrValue, $widthAttrValue, $heightAttrValue)
	{
		return Module::getHtdocsPath('medium.php') . $node->path . ':' . $srcAttrValue;
	}
	public static function renderURLForLink(Node $node, $locale, $baseURL, $hrefAttrValue)
	{
		if(!empty($hrefAttrValue) && substr($hrefAttrValue, 0, 7) == 'node://') {
			return $baseURL . substr($hrefAttrValue, 6);
		} else {
			return $hrefAttrValue;
		}
	}
	public static function renderNode(Node $node, $rootDir, $locale, $contentType, $baseURL)
	{
		$doc = new Doc;
		// die('<?xml encoding="UTF-8">' . $node->getRawContent($contentType, $locale));
		@$doc->loadHTML(
			'<?xml encoding="UTF-8">' . $node->getRawContent($contentType, $locale)
		);
		//$doc->loadHTML('<?xml encoding="UTF-8">' . $json->html);
		$killList = array();
		foreach($doc->getElementsByTagName('a') as $linkEl) {
			/* @var El $linkEl */
			$href = $linkEl->getAttribute('href');
			$linkEl->setAttribute('href', call_user_func_array(array(get_called_class(), 'renderURLForLink'), array($node, $locale, $baseURL, $href)));
		}
		foreach($doc->getElementsByTagName('img') as $imgEl) {
			/* @var El $imgEl */
			$src = call_user_func_array(array(get_called_class(), 'renderURLForMedium'), array(
				$node,
				$imgEl->getAttribute('src'),
				$width  = $imgEl->getAttribute('width'),
				$height = $imgEl->getAttribute('height')
			));

			foreach(array('width' => $width, 'height' => $height) as $sizeAttributeName => $sizeAttributeValue) {
				if($sizeAttributeValue !== (string)(int) $sizeAttributeValue) {
					$imgEl->removeAttribute($sizeAttributeName);
				}
			}

			$imgEl->setAttribute('src', $src);
		}
		foreach($doc->getElementsByTagName('div') as $divEl) {
			/* @var El $appEl */
			$classNameAttr = $divEl->getAttribute('data-foomo-app');
			$linkerAttr = $divEl->getAttribute('data-foomo-linker');
			if($classNameAttr) {
				self::renderApp($rootDir, $baseURL, $locale, $doc, $divEl, $linkerAttr);
			} else if($linkerAttr) {
				self::renderLinker($rootDir, $baseURL, $locale, $doc, $divEl, $linkerAttr);
			}
		}
		foreach($killList as $killEl) {
			$doc->removeChild($killEl);
		}
		// i wish there was a nicer way to strip html
		$html = $doc->saveHTML();
		$html = substr($html, strpos($html, '<body>') + 6);
		return substr($html, 0, strpos($html, '</body>'));
	}

	private static function renderApp($rootDir, $baseURL, $locale, $doc, $appEl, $linkerAttr)
	{
		$className = str_replace('.', '\\', (string) $appEl->getAttribute('data-foomo-app'));
		if(class_exists($className)) {
			$dataElements = $appEl->getElementsByTagName('script');
			if($dataElements->length > 0) {
				$data = json_decode($dataElements->item(0)->textContent);
			} else {
				$data = null;
			}
			call_user_func_array(array($className, 'render'), array($data, $doc, $appEl));
		} else {
			$killList[] = $appEl;
		}
	}

	private static function renderLinker($rootDir, $baseURL, $locale, Doc $doc, El $el, $linkerDataString)
	{
		$linkerDataString = substr($linkerDataString, 6);
		$parts = explode(':', $linkerDataString);
		$path = $parts[0];
		$contentType = $parts[1];
		$node = Content::getNode($rootDir, $path);
		foreach($node->index as $childNodeId) {
			$childNode = $node->getChildNodeById($childNodeId);
			$newDoc = new Doc();
			$newDoc->loadHTML('<div><a href="' . htmlspecialchars($baseURL . $childNode->path) . '">' . self::renderNode($childNode, $rootDir, $locale, $contentType, $baseURL) . '</a></div>');
			$newEl = $newDoc->getElementsByTagName('div')->item(0);
			$newEl = $doc->importNode($newEl, true);
			$el->appendChild($newEl);
		}
	}
}