<?php
	/** @var \yii\web\View $this */
	/** @var \bedezign\yii2\desktop\Shortcut $shortcut */
	/** @var int $x */
	/** @var int $y */

	$icon = $shortcut->icon->render(\bedezign\yii2\desktop\Icon::DISPLAY_DESKTOP);
	$anchor = \yii\helpers\ArrayHelper::merge(
		['id' => $shortcut->id, 'style' => "left: {$x}px; top: {$y}px", 'class' => 'application_shortcut'],
		$shortcut->anchor);
?>
<a <?= \yii\helpers\Html::renderTagAttributes($anchor) ?>>
	<?= $icon ?>
	<?php if ($shortcut->title): ?><span class="title"><?= $shortcut->title ?></span><?php endif; ?>
</a>
