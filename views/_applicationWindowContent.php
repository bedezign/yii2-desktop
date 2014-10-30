<?php
/**
 * Default content for an application: an IFrame for the given route
 */
	$id = 'window_' . \yii\helpers\Html::encode($application->id);
	$url = \yii\helpers\Url::to($application->route);
?>
<iframe name="<?= $id ?>" scrolling="auto" class="window_frame" frameborder="0" data-url="<?= $url ?>">

</iframe>
