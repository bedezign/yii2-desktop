<?php
	$showDesktopUrl = $desktop->assetsUrl . '/images/gui/icon_desktop.png';
	$showDesktop = 'Show Desktop';
?>
<div class="dock">
	<a href="#" class="show_desktop" title="<?= $showDesktop ?>"><img src="<?= $showDesktopUrl ?>"></a>
	<ul>
		<?php foreach($applications as $application)
				echo $application->renderDockButton(); ?>
	</ul>
</div>
