<?php

	/** @var \bedezign\yii2\desktop\Desktop $desktop */
	/** @var \bedezign\yii2\desktop\Menu $menu */

	if (!$menu)
		return;
?>
<div class="menus">
	<?php if ($globalMenu = $menu->getShortcuts(true)): ?>
		<?php foreach ($globalMenu as $rootShortcut): ?>
			<div class="global-menu menu">
				<?= $this->render('_rootMenuShortcut', ['shortcut' => $rootShortcut]) ?>
			</div>
		<?php endforeach ?>
	<?php endif; ?>

	<?php foreach ($menu->getShortcuts() as $application => $applicationMenus): ?>
		<div class="application_menu menu" id="<?= $application ?>_menu">
			<?php foreach ($applicationMenus as $rootShortcut): ?>
				<?= $this->render('_rootMenuShortcut', ['shortcut' => $rootShortcut]) ?>
			<?php endforeach ?>
		</div>
	<?php endforeach ?>
</div>
