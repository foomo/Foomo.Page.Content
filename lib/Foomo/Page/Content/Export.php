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
use Foomo\ContentServer\Vo\Content\RepoNode;
use DOMDocument as Doc;
use DOMElement as El;

class Export
{
	const REGION_UNIVERSE = 'universe';
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
		foreach($node->content['default'] as $lang => $contentFile) {
			switch(true) {
				case substr($contentFile, -3) == '.md':
					$repoNode->mimeType = 'text/markdown';
					break;
				case substr($contentFile, -5) == '.html':
				default:
					$repoNode->mimeType = 'text/html';
			}
			break;
		}
		$repoNode->handler = $repoNode->mimeType;
		$repoNode->hidden = array('universe' => array('de' => false, 'en' => false));
		foreach($node->names as $language => $name) {
			$repoNode->addName(self::REGION_UNIVERSE, $language, $name);
		}
		if(is_array($node->nodes)) {
			foreach($node->index as $childIndex) {
				$repoNode->addNode(self::nodeToRepoNode($node->nodes[$childIndex]));
			}
		}
		$repoNode->addRegion(self::REGION_UNIVERSE);
		foreach(array('de', 'en') as $language) {
			$repoNode->addURI(self::REGION_UNIVERSE, $language, '/' . $language . $node->path);
			foreach(array('full', 'summary') as $contentType) {
				// we have no regions
				foreach(self::extractLinkIds($language, $contentType, $node) as $linkId) {
					// $repoNode->addLinkId(self::REGION_UNIVERSE, $language, $linkId);
				}
			}
		}

		$repoNode->data = self::getNodeData($node);
		return $repoNode;
	}
	private static function getNodeData(Node $node)
	{
		$copy = array();
		foreach($node as $k => $v) {
			if(in_array($k, array('nodes'))) {
				continue;
			}
			$copy[$k] = $v;
		}
		return $copy;
	}
	private static function extractLinkIds($language, $contentType, Node $node) {
		$ret = array();
		$rawContent = $node->getRawContent($contentType, $language);
		if(!empty($rawContent)) {
			$doc = new Doc;
			libxml_clear_errors();
			libxml_use_internal_errors(true);
			$doc->loadHTML($rawContent);
			foreach(libxml_get_errors() as $e) {
				/* @var $e \LibXMLError */
				//trigger_error('xml e ' . $e->message);
			}
			libxml_clear_errors();
			foreach($doc->getElementsByTagName('a') as $linkEl) {
				/* @var El $linkEl */
				$href = $linkEl->getAttribute('href');
				if(!empty($href) && substr($href, 0, 7) == 'node://') {
					$ret[] = self::pathToId(substr($href, 6));
				}
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
		return $path;
	}
}