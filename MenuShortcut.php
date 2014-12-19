<?php
/**
 * Represents a menu shortcut
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @copyright 2014 B&E DeZign
 */


namespace bedezign\yii2\desktop;


class MenuShortcut
	extends components\Component
{
	const STATUS_DISABLED = 0;
	const STATUS_ENABLED  = 1;

	/** @var string   Unique ID within the menu */
	public $id = null;

	/** @var string   The title  */
	public $title = null;

	/**
	 * Whether the item can be clicked or not
	 * @var int
	 */
	public $status = self::STATUS_ENABLED;

	/**
	 * Attributes for the anchor. By default this just contains a 'href' = '#application_id'
	 * @var array
	 */
	public $anchor    = null;

	/**
	 * Child menu items, if any
	 * @var self[]
	 */
	public $children = [];

	/**
	 * Indicates that the menu should be added to all application menus
	 * @var bool
	 */
	public $sticky = false;
}