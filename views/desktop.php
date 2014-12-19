<?php

	use yii\helpers\Html;

	/** @var \yii\web\View $this */
	/** @var \bedezign\yii2\desktop\Desktop $desktop */
	/** @var \bedezign\yii2\desktop\Application[] $applications */
	/** @var \bedezign\yii2\desktop\Shortcut[] $shortcuts*/

	$desktopId = 'desktop_' . Html::encode($desktop->id);

	$options = [
		'eventUrl' => $desktop->eventUrl
	];
	$options = \yii\helpers\Json::encode($options);

	$this->registerJs("desktop.boot($options);");
?>
<div class="desktop_wrapper">
	<?= $this->render('_menu', ['desktop' => $desktop, 'menu' => $menu]) ?>
	<div id="<?= $desktopId ?>" class="desktop">
		<?php foreach($shortcuts as $shortcut) echo $shortcut->render(); ?>
		<?php foreach($applications as $application) echo $application->renderWindow(); ?>
	</div>
	<?= $this->render('_dock', ['desktop' => $desktop, 'applications' => $applications]) ?>
</div>
