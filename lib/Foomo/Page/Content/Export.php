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

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
use Foomo\SimpleData\VoMapper;
use Foomo\ContentServer\Vo\Content\RepoNode;
use DOMDocument as Doc;
use DOMElement as El;

class Export
{
	/**
	 * @param Node $node
	 *
	 * @return RepoNode
	 */
	public static function nodeToRepoNode(Node $node)
	{
		$repoNode = new RepoNode();
		$repoNode->id = self::getId($node);
		$repoNode->handler = 'foomo';
		$repoNode->addGroup('www');
		$repoNode->mimeType = 'text/html';
		$repoNode->hidden = false;
		foreach($node->names as $language => $name) {
			$repoNode->addName($language, $name);
		}
		if(is_array($node->nodes)) {
			foreach($node->nodes as $childNode) {
				$repoNode->addNode(self::nodeToRepoNode($childNode));
			}
		}
		$region = 'universe';
		$repoNode->addRegion($region);
		foreach(array('de', 'en') as $language) {
			$repoNode->addURI($region, $language, '/' . $language . $node->path);
			foreach(array('full', 'summary') as $contentType) {
				// we have no regions
				foreach(self::extractLinkIds($language, $contentType, $node) as $linkId) {
					$repoNode->addLinkId($region, $language, $linkId);
				}
			}
		}
		return $repoNode;
	}
	private static function extractLinkIds($language, $contentType, Node $node) {
		$ret = array();
		$doc = new Doc;
		$doc->loadHTML(
			$node->getRawContent($contentType, $language)
		);
		foreach($doc->getElementsByTagName('a') as $linkEl) {
			/* @var El $linkEl */
			$href = $linkEl->getAttribute('href');
			if(!empty($href) && substr($href, 0, 7) == 'node://') {
				$ret[] = self::pathToId(substr($href, 6));
			}
		}
		return $ret;
	}
	private static function getId(Node $node)
	{
		return self::pathToId($node->path);
	}
	private static function pathToId($path)
	{
		return md5($path);
	}
}