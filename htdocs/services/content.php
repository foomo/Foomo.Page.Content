<?php
header('Content-Type: application/json');
echo json_encode(
	Foomo\Page\Content\Export::nodeToRepoNode(
		\Bestbytes\Site\Module::getContentRootNode()
	),
	JSON_PRETTY_PRINT
);