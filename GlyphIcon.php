<?php
/**
 * With Yii2 being compatible with Twitter Bootstrap, it only makes sense to support glyphicons as application icon.
 *
 * Set "image" to the glyphicon name (without prefixes) to change the icon. The default icon is "folder-open"
 */

namespace bedezign\yii2\desktop;

use yii\helpers\Html;

class GlyphIcon extends Icon
{
	public function render($type)
	{
		// Obviously we need the bootstrap assets for this
		\yii\bootstrap\BootstrapPluginAsset::register($this->desktop->view);

		$glyph = $this->image;
		if (!$glyph)
			$glyph = 'folder-open';

		$attributes = ['class' => "glyphicon glyphicon-$glyph"];

		$styles = [];
		switch ($type) {
			case self::DISPLAY_DOCK:      $styles = ['position' => 'relative', 'top' => '1px', 'font-size' => '16px', 'padding-right' => '5px']; break;
			case self::DISPLAY_TITLEBAR : $styles = ['float' => 'left', 'margin' => '4px 8px 0 0', 'font-size' => '20px']; break;
			case self::DISPLAY_DESKTOP :  $styles = ['font-size' => '32px']; break;
		}

		 Html::addCssStyle($attributes, $styles);
		return Html::tag('span', '', $attributes);
	}
}