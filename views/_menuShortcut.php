<?php
	$hasChildren = count($shortcut->children);
	$icon = $shortcut->getIcon(false) ? $shortcut->getIcon()->render(\bedezign\yii2\desktop\Icon::DISPLAY_MENU) . ' ' : '';
?>
<li>
	<a class="<?= $hasChildren ? 'submenu menu-trigger' : '' ?>"><?= $icon . $shortcut->title ?></a>
	<?php if ($hasChildren): ?>
		<ul class="menu submenu">
			<?php foreach ($shortcut->children as $child): ?>
				<?= $this->render('_menuShortcut', ['shortcut' => $child]) ?>
			<?php endforeach; ?>
		</ul>
	<?php endif ?>
</li>