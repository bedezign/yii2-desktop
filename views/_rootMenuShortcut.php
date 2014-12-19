<?php
	$icon = $shortcut->getIcon(false) ? $shortcut->getIcon()->render(\bedezign\yii2\desktop\Icon::DISPLAY_MENU) . ' ' : '';
?>
<a href="#" class="menu menu-trigger"><?= $icon . $shortcut->title ?></a>
<?php if (count($shortcut->children)): ?>
<ul class="menu">
	<?php foreach ($shortcut->children as $child): ?>
		<?= $this->render('_menuShortcut', ['shortcut' => $child]) ?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
