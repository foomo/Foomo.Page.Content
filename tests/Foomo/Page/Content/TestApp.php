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

use DOMDocument as Doc;
use DOMElement as El;

class TestApp extends AbstractApp
{
	/**
	 * @param $data
	 * @param \DOMDocument $doc
	 * @param \DOMElement $el
	 * @return null
	 */
	public static function render($data, Doc $doc, El $el)
	{
		$newDoc = new Doc();
		$newDoc->loadHTML('<div><h1>Hello world</h1><pre>' . time() . ' ' . htmlspecialchars(var_export($data, true)) . '</pre></div>');
		$newEl = $newDoc->getElementsByTagName('div')->item(0);
		$newEl = $doc->importNode($newEl, true);
		$el->parentNode->replaceChild($newEl, $el);
	}
}