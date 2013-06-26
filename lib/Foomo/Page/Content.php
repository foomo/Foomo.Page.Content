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

namespace Foomo\Page;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
use Foomo\Cache\MockObjects\Node;
use Foomo\SimpleData;

class Content
{
	public static function getNode($rootDir, $id)
	{
		$path = '/';
		$rootNode = self::loadTree($rootDir);
		return self::searchNode($rootNode, $path, $id);
	}
	private static function loadTree($srcDir)
	{
		$rawData = SimpleData::read($srcDir);
		$node = new Content\Node();
		SimpleData\VoMapper::map($rawData->data, $node);
		self::addPath($node, '/');
		return $node;
	}
	private static function addPath(Content\Node $node, $path)
	{
		if(!empty($node->id)) {
			$path .= ((substr($path, -1) == '/')?'':'/') . $node->id;
		}
		$node->path = $path;
		foreach($node->nodes as $childNode) {
			self::addPath($childNode, $path);
		}
	}
	private static function searchNode(Content\Node $node, $path, $id)
	{
		if($node->path == $id) {
			return $node;
		} else {
			foreach($node->nodes as $childNode) {
				$foundNode = self::searchNode($childNode, $path, $id);
				if(!is_null($foundNode)) {
					return $foundNode;
				}
			}
		}
		return null;
	}
}