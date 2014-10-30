<?php
/**
 *
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @category
 * @copyright 2014 B&E DeZign
 */

namespace bedezign\yii2\desktop\assets;

class DesktopAssets extends \yii\web\AssetBundle
{
	public $sourcePath = __DIR__;

	public $js = ['javascript/desktop.js'];

	public $css = ['stylesheets/desktop.css'];

	public $depends = ['yii\jui\JuiAsset'];

	public $publishOptions = ['forceCopy' => true];
}