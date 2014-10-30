<?php
/**
 * Base component for anything that can be used on the desktop
 */

namespace bedezign\yii2\desktop\components;

use bedezign\yii2\desktop\Desktop;

abstract class Component extends \yii\base\Component
{
	use PropertyExtractor;

	/** @var Desktop */
	protected $desktop   = null;

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
		return $properties;
	}

	public function canHandleEvent($type)
	{
		return false;
	}

	/**
	 * @param $data
	 * @return bool      true if anything changed, false if not
	 */
	function event($data)
	{
		return false;
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
}