<?php
/**
 * Placeholder for
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @copyright 2014 B&E DeZign
 */

namespace bedezign\yii2\desktop;

class Menu
	extends components\Component
{
	/** @var MenuShortcut[] */
	protected $menus = null;

	public function setDesktop(Desktop $desktop)
	{
		parent::setDesktop($desktop);
		foreach ($this->menus as $group)
			foreach ($group as $menu)
				$this->setDesktopRecursive($menu);
	}

	/**
	 * Add a new main menu. Sticky means that it will be shown for all applications.
	 *
	 * @param MenuShortcut $menu
	 * @param Application $application     The application this menu is linked to, if null it is always shown
	 */
	public function addMenu(MenuShortcut $menu, Application $application = null)
	{
		$context = $application ? $application->getApplicationId() : 'global';
		if (!isset($this->menus[$context]))
			$this->menus[$context] = [];
		$this->menus[$context][] = $menu;
	}

	public function getShortcuts($global = false)
	{
		if ($global) {
			if (isset($this->menus['global']))
				return $this->menus['global'];
			return null;
		}

		$menus = $this->menus;
		unset($menus['global']);
		return $menus;
	}

	public function setDesktopRecursive(MenuShortcut $menu)
	{
		$menu->setDesktop($this->desktop);
		foreach ($menu->children as $menu)
			$this->setDesktopRecursive($menu);
	}
}