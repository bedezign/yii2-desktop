<?php
/**
 * Base component for anything that can be used on the desktop
 */

namespace bedezign\yii2\desktop\components;

use bedezign\yii2\desktop\Desktop;
use bedezign\yii2\desktop\Icon;

abstract class Component extends \yii\base\Component
{
	use PropertyExtractor;

	/** @var Desktop */
	protected $desktop   = null;

	protected $icon      = null;

	/**
	 * This function returns the desktop.js compatible javascript code that is required to restore the component into
	 * its previous state. This code will be called as part of the initialisation.
	 * @return string
	 */
	public function getRestoreScript()
	{
		return '';
	}

	/**
	 * Returns the instance as a \Yii::createObject() compatible array
	 * @return array
	 */
	public function getConfig()
	{
		$properties = $this->getPublicProperties();
		$properties['class'] = static::className();

		if ($this->icon)
			// A bit weird since an icon itself is also a component, but it will work :)
			$properties['icon'] = $this->icon->getConfig();
		return $properties;
	}

	/**
	 * @return Desktop
	 */
	public function getDesktop()
	{
		return $this->desktop;
	}

	/**
	 * @param Desktop $desktop
	 */
	public function setDesktop(Desktop $desktop)
	{
		$this->desktop = $desktop;
	}

	/**
	 * @param Icon|string      $icon     If a string is specified, a regular icon is created and the string is used as image
	 */
	public function setIcon($icon)
	{
		if (is_string($icon)) {
			$_icon = new Icon();
			$_icon->image = $icon;
			$icon =  $_icon;
		}

		$this->icon = $icon;
	}

	public function getIcon($alwaysReturnInstance = true)
	{
		$icon = $this->icon;
		if (!$icon && $alwaysReturnInstance)
			$icon = new Icon;

		if ($icon)
			$icon->desktop = $this->desktop;

		return $icon;
	}

	public function getHasIcon()
	{
		return $this->icon != null;
	}
}