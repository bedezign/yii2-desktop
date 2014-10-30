<?php
	/** @var \bedezign\yii2\desktop\Application $application */
	/** @var \yii\web\View $this */

	use \yii\helpers\Html;
	$title = $application->title;
	$icon  = Html::img($application->iconUrl, ['alt' => $title]);

	$attributes = ['id' => 'window_' . $application->applicationId, 'class' => 'application_window absolute'];
	if ($application->windowPosition)
		Html::addCssStyle($attributes, $application->windowPosition);

	if ($application->windowMaximised)
		Html::addCssClass($attributes, 'window_maximized');

	if ($application->sessionData)
		$attributes['data-desktop-session'] = \yii\helpers\Json::encode($application->sessionData);
?>

<div <?= Html::renderTagAttributes($attributes) ?>>
	<div class="window_titlebar">
		<div class="window_icon"><?= $icon ?></div>
		<div class="window_title"><?= $title ?></div>
		<div class="window_buttons">
			<a href="#" class="window_button minimize"></a>
			<a href="#" class="window_button maximize"></a>
			<a href="#<?= $application->applicationId ?>" class="window_button close"></a>
		</div>
	</div>

	<div class="window_content">
		<?= $application->renderContent() ?>
	</div>

	<div class="window_footer"></div>
	<span class="ui-resizable-handle ui-resizable-se"></span>
</div>
