<?php

	/** @var \bedezign\yii2\desktop\Desktop $desktop */
	/** @var \bedezign\yii2\desktop\Menu $menu */

	if (!$menu)
		return;
?>
<div class="menus">
	<?php if ($global = $menu->getShortcuts(true)): ?>
		<div class="global-menu menu">
			<?= $global ? $this->render('_rootMenuShortcut', ['shortcut' => $global]) : '' ?>
		</div>
	<?php endif; ?>

	<?php foreach ($menu->getShortcuts() as $application => $applicationMenus): ?>
		<div class="application-menu menu" id="menu-<?= $application ?>">
			<?php foreach ($applicationMenus as $rootShortcut): ?>
				<?= $this->render('_rootMenuShortcut', ['shortcut' => $rootShortcut]) ?>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
</div>
