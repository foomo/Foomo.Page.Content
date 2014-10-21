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

namespace Foomo\Page\Content\Renderer;

use Foomo\Page\Content;
use Foomo\Yaml;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Markdown
{
	public static function preRenderNode(Content\Node $node, $rootDir, $locale, $contentType, $baseURL)
	{
		$pd = new \ParsedownExtra();
		return self::rewriteApps($pd->text($node->getRawContent('', $locale)));
	}

	private static function rewriteApps($html)
	{
		$doc = new \DOMDocument;
		//libxml_use_internal_errors(true);
		$doc->loadHTML($html);
		//libxml_clear_errors();
		$appElements = array();
		$bodyElement = $doc->getElementsByTagName('body')->item(0);
		/* @var $childEl \DOMElement */
		foreach($bodyElement->childNodes as $childEl) {
			if($childEl->hasChildNodes() && $childEl->tagName == 'pre') {
				if($childEl->firstChild->hasChildNodes()  && $childEl->firstChild->tagName == 'code') {
					if($childEl->firstChild->getAttribute('class') == 'language-App') {
						$appElements[] = $childEl;
					}
				}
			}
		}
		foreach($appElements as $childEl) {
			$appDiv = $doc->createElement('div');
			$yaml = Yaml::parse($childEl->firstChild->nodeValue);
			$appDiv->setAttribute('data-foomo-app', $yaml['class']);
			$appDiv->appendChild(
				$script = $doc->createElement('script', json_encode($yaml['data']))
			);
			$script->setAttribute('type', 'json');
			$childEl->parentNode->replaceChild($appDiv, $childEl);
		}
		return $doc->saveHTML();
	}

}