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

class Node
{
	/**
	 * @var string
	 */
	public $id;
	/**
	 * unique path
	 *
	 * @var string
	 */
	public $path;
	/**
	 * hash of names
	 *
	 * @var mixed
	 */
	public $names = array();
	/**
	 * public content in right order
	 *
	 * @var string[]
	 */
	public $index = array();
	/**
	 * hash of html contents of this node
	 *
	 * @var hash
	 */
	public $content = array();
	/**
	 * @var hash
	 */
	public $media = array();
	/**
	 * @var hash
	 */
	public $nodes = array();
	public function setNodes($value)
	{
		foreach($value as $id => $nodeData) {
			$node = new Node;
			$this->nodes[$id] = $node;
			VoMapper::map($nodeData, $node);
			$node->id = $id;
		}
	}
	public function setContent($value)
	{
		foreach($value as $key => $value) {
			if(is_array($value)) {
				$this->content[$key] = $value;
			} else {
				if(!isset($this->content['default'])) {
					$this->content['default'] = array();
				}
				$this->content['default'][$key] = $value;
			}
		}
	}
	/**
	 * @param string $path sth like 'summary'
	 * @param string $locale
	 * @return bool
	 */
	public function hasContent($path, $locale)
	{
		return isset($this->content[$path]) && isset($this->content[$path][$locale]);
	}
	public function getRawContent($path, $locale)
	{
		if($this->hasContent($path, $locale)) {
			return file_get_contents($this->content[$path][$locale]);
		} else if(isset($this->content['default'][$locale])) {
			return file_get_contents($this->content['default'][$locale]);
		} else {
			return file_get_contents($this->content[$locale]);
		}
	}
	/**
	 * my name
	 *
	 * @param $locale
	 *
	 * @return string
	 */
	public function getName($locale)
	{
		if(isset($this->names[$locale])) {
			return $this->names[$locale];
		} else if(count($this->name) > 0) {
			$keys = array_keys($this->names);
			return $this->names[$keys[0]];
		} else {
			return $this->id;
		}
	}

	/**
	 * @param string $id
	 *
	 * @return Node
	 */
	public function getChildNodeById($id)
	{
		foreach($this->nodes as $childNode) {
			if($childNode->id == $id) {
				return $childNode;
			}
		}
	}

	/**
	 * @param string $name
	 * @return Medium
	 */
	public function getMedium($name)
	{
		if(isset($this->media[$name])) {
			$ret = new Medium($this->media[$name]);
			return $ret;
		} else if(strpos($name, '/') !== false) {
			$path = explode('/', $name);
			$access = &$this->media;
			foreach($path as $key) {
				if(isset($access[$key])) {
					$access = &$access[$key];
				} else {
					return null;
				}
			}
			$ret = new Medium($access);
			return $ret;
		}
	}
}