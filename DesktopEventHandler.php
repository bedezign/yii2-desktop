<?php
/**
 *
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @copyright 2014 B&E DeZign
 */

namespace bedezign\yii2\desktop;

interface DesktopEventHandler
{
	public function canHandleEvent($type);
	public function event($data);
}