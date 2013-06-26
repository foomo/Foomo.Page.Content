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
class ContentTest extends \PHPUnit_Framework_TestCase
{
	private $srcDir;
	public function setUp()
	{
		$this->srcDir = __DIR__ . DIRECTORY_SEPARATOR . 'Content' . DIRECTORY_SEPARATOR . 'mock';
	}
	public function testGetNode()
	{
		$rootNode = Content::getNode($this->srcDir, '/');
		$this->assertInstanceOf('Foomo\\Page\\Content\\Node', $rootNode);
		$this->assertEquals(array('home', 'foo'), $rootNode->index);
		$blaNode = Content::getNode($this->srcDir, '/foo/bla');
		$this->assertInstanceOf('Foomo\\Page\\Content\\Node', $blaNode);
		$this->assertEquals('Bla', $blaNode->names['de']);
	}
}