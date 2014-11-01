<?php
/**
 *
 *
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

		$attributes = ['src' => $image];

		$styles = [];
		switch ($type) {
			case self::DISPLAY_DOCK:      $styles = ['position' => 'relative', 'top' => '5px', 'height' => '22px']; break;
			case self::DISPLAY_TITLEBAR : $styles = ['float' => 'left', 'margin' => '4px 5px 0 0', 'height' => '20px' ]; break;
			case self::DISPLAY_DESKTOP :  $styles = ['height' => '32px']; break;
		}

		Html::addCssStyle($attributes, $styles);
		return Html::img($attributes['src'], $attributes);
	}
}