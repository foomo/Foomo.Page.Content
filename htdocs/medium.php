<?php

$baseURL = \Foomo\Page\Content\Module::getHtdocsPath() . '/' . basename(__FILE__);
$pathParts = explode(':', substr($_SERVER['REQUEST_URI'], strlen($baseURL)));
if(count($pathParts) == 2) {
	$path = $pathParts[0];
	$mediumName = $pathParts[1];
	$rootDir = \Foomo\Page\Content\Module::getTestsDir('Foomo/Page/Content/mock');
	$node = \Foomo\Page\Content::getNode($rootDir, $path);
	$node->getMedium($mediumName)->stream();
}
