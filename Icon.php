<?php
/**
 * Represents a (regular image) icon that knows how to render itself on all different parts of the desktop
 * If the desktop has an iconPath configured and the image is a relative url, it will use the iconPath as baseUrl
 */

namespace bedezign\yii2\desktop;

use yii\helpers\Html;

class Icon extends components\Component
{
	const DISPLAY_DESKTOP   = 1;
	const DISPLAY_DOCK      = 2;
	const DISPLAY_TITLEBAR  = 3;
	const DISPLAY_MENU      = 4;

	public $image = null;

	public function render($type)
	{
		$image = $this->image;
		if (!$image)
			$image = $this->desktop->assetsUrl . '/images/icon_application.png';
		else if (\yii\helpers\Url::isRelative($image) && $this->desktop->iconPath)
			$image = rtrim($this->desktop->iconPath, '/') . '/' . $image;

		$attributes = ['src' => $image];

		$styles = [];
		switch ($type) {
			case self::DISPLAY_MENU:      $styles = ['height' => '16px', 'position' => 'relative', 'top' => '-2px']; break;
			case self::DISPLAY_DOCK:      $styles = ['height' => '16px']; break;
			case self::DISPLAY_TITLEBAR : $styles = ['float' => 'left', 'margin' => '4px 5px 0 0', 'height' => '20px' ]; break;
			case self::DISPLAY_DESKTOP :  $styles = ['height' => '32px']; break;
		}

		Html::addCssStyle($attributes, $styles);
		return Html::img($attributes['src'], $attributes);
	}
}