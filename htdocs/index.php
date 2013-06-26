<?php

$path = '/';
$locale = 'de';
$baseURL = \Foomo\Page\Content\Module::getHtdocsPath() . '/index.php';
$rootDir = \Foomo\Page\Content\Module::getTestsDir('Foomo/Page/Content/mock');

$node = \Foomo\Page\Content::getNode($rootDir, $path);

function navigation($baseURL, $locale, \Foomo\Page\Content\Node $node, &$navigation, $path)
{
		if($node->id) {
			$navigation .=
				'<li>
					<a href="' . $baseURL .  htmlspecialchars($path) . '">'
						. htmlspecialchars($node->getName($locale) . ' ' . $path) .
					'</a></li>';
		}
		$navigation .= count($node->index) > 0?'<ul>':'';
		foreach($node->index as $childId) {
			$childNode = $node->getChildNodeById($childId);
			navigation($baseURL, $locale, $childNode, $navigation, $path . '/' . $childId);
		}
		$navigation .= count($node->index) > 0?'</ul>':'';
}

$navigation = '';

navigation($baseURL, $locale, $node, $navigation, '');

$selectedPath = substr($_SERVER['REQUEST_URI'], strlen($baseURL));


?><html>
	<body>
		<header>
			<?= $navigation ?>
		</header>
		<div class="crumb">
			<?= htmlspecialchars($selectedPath) ?>
		</div>
			<?=
				\Foomo\Page\Content\Renderer::renderNode(
					\Foomo\Page\Content::getNode($rootDir, $selectedPath),
					$rootDir,
					$locale,
					'full',
					$baseURL
				)
			?>
		<footer>
		</footer>
	</body>
</html>