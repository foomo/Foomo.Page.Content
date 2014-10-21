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
		if(!empty($value)) {
			$this->nodes = array();
		}
		foreach($value as $id => $nodeData) {
			$node = new Node;
			$this->nodes[$id] = $node;
			VoMapper::map($nodeData, $node);
			$node->id = $id;//md5($node->path);
		}
	}
	public function setContent($value)
	{
		if(!empty($value)) {
			if(is_string($value)) {
				$value = array('en' => $value);
			}
			foreach($value as $key => $value) {
				if(is_array($value)) {
					$this->content[$key] = $value;
				} else if(is_object($value)) {
					$this->content[$key] = (array) $value;
				} else {
					if(!isset($this->content['default'])) {
						$this->content['default'] = array();
					}
					$this->content['default'][$key] = $value;
				}
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
	public function getContentFile($path, $locale)
	{
		if($this->hasContent($path, $locale)) {
			return $this->content[$path][$locale];
		} else if(isset($this->content['default'][$locale])) {
			return $this->content['default'][$locale];
		} else if(isset($this->content['default'][$locale])){
			return $this->content[$locale];
		} else if(!empty($this->content['default'])) {
			$keys = array_keys($this->content['default']);
			if(count($keys) > 0) {
				return $this->content['default'][$keys[0]];
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	public function getContentFileSuffix($path, $locale)
	{
		$file = basename((string)$this->getContentFile($path, $locale));
		if(!empty($file) && strpos($file, '.') !== 0) {
			$reversed = strrev($file);
			return strrev(substr($reversed, 0, strpos($reversed, '.')));
		} else {
			return null;
		}
	}
	public function getRawContent($path, $locale)
	{
		$file = self::getContentFile($path, $locale);
		if(!empty($file) && file_exists($file)) {
			return file_get_contents($file);
		} else {
			return null;
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
	public function setNames($names)
	{
		if(is_object($names)) {
			$names = (array) $names;
		}
		$this->names = $names;
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