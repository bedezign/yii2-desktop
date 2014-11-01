<?php
/**
 *
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @category
 * @copyright 2014 B&E DeZign
 */


namespace bedezign\yii2\desktop;

use yii\base\InvalidConfigException;

class Desktop extends \yii\base\Widget
{
	use components\PropertyExtractor;

	public $grid = [
		/**
		 * Distance (in pixels) between 2 icons on the desktop (the first icon will be at gridSize/2
		 * Note: if you custom place an icon, the grid does not really apply
		 * @var int
		 */
		'size'      => 100,
		'margin'    => 0,

		/**
		 * Amount of icons to place next to each other before jumping to the next line
		 * @var int
		 */
		'columns'   => 5,
	];

	/**
	 * Route within your system that can pass back event calls (like moved icons etc) to the desktop for
	 * permanent storage. Basically you just instantiate the desktop instance and pass whatever arguments you got
	 * to the "callback()" function.
	 * @var string
	 */
	public $eventUrl   = null;

	/**
	 * A unique ID for this desktop.
	 * This is something you choose and it only matters during loading/saving and interactions
	 * @var string
	 */
	public $id = null;

	/**
	 * At runtime, this variable contains the assets base folder
	 * @var string
	 */
	public $assetsUrl = null;

	/** @var Application[] */
	protected $applications = [];

	/** @var Shortcut[] */
	protected $shortcuts = [];

	/** @var [int, int]  Keeps track of the icon auto placement  */
	protected $iconAutoPosition = [0, 0];

	public function init()
	{
		if (!$this->id)
			throw new InvalidConfigException('"id" is required');

		parent::init();
	}

	public function run()
	{
		$this->assetsUrl = assets\DesktopAssets::register($this->view)->baseUrl;
		return $this->render('desktop', ['desktop' => $this, 'applications' => $this->applications, 'shortcuts' => $this->shortcuts]);
	}

	/**
	 * Proxy function that receives all interaction with the desktop and updates the internal data.
	 * This function should be called by your controller on whatever feedback  you get from the desktop (resize, move etc).
	 * Please configure eventUrl to the correct route so the javascript can call your controller action
	 *
	 * @return bool      true if data has changed, false if not (on true you might want to save the desktop)
	 */
	public function event()
	{
		$request = \Yii::$app->request;

		$type = $request->post('action');
		$application = $this->findApplicationById(Application::toRegularId($request->post('application')));
		if ($application && $application->canHandleEvent($type))
			return $application->event($request->post());

		$shortcut = $this->findShortcutById($request->post('shortcut'));
		if ($shortcut && $shortcut->canHandleEvent($type))
			return $shortcut->event($request->post());

		return false;
	}

	/**
	 * Add an application to the desktop
	 * @param Application $application
	 * @param bool $createDesktopShortcut If you want to automatically create a shortcut on the desktop
	 * @return bool
	 * @throws InvalidConfigException
	 */
	public function registerApplication(Application $application, $createDesktopShortcut = true)
	{
		if (array_key_exists($application->id, $this->applications))
			throw new InvalidConfigException("application {$application->id} is already defined");

		$application->setDesktop($this);
		$this->applications[$application->id] = $application;
		if ($createDesktopShortcut)
			$this->registerShortcut(Shortcut::fromApplication($application));

		return true;
	}

	public function findApplicationById($application)
	{
		if (array_key_exists($application, $this->applications))
			return $this->applications[$application];

		return null;
	}

	public function registerShortcut(Shortcut $shortcut)
	{
		$shortcut->setDesktop($this);

		// No id assigned, create one
		if (!$shortcut->id) {
			$last = 0;
			foreach ($this->shortcuts as $existingShortcut)
				if (substr($existingShortcut->id, 0, 9) == 'shortcut_')
					$last = max($last, intval(substr($existingShortcut->id, 9)));
			$shortcut->id = 'shortcut_' . ($last + 1);
		}
		$this->shortcuts[$shortcut->id] = $shortcut;
		return true;
	}

	public function findShortcutById($shortcut)
	{
		if (array_key_exists($shortcut, $this->shortcuts))
			return $this->shortcuts[$shortcut];

		return null;
	}

	/**
	 * Returns the center position for a new shortcut
	 * @return array['x' => int, 'y' => int]
	 */
	public function getShortcutPosition()
	{
		if ($this->iconAutoPosition[0] + 1 >= $this->grid['columns']) {
			// Amount of columns reached? Next row
			$this->iconAutoPosition[0] = 0;
			$this->iconAutoPosition[1] ++;
		}
		else
			$this->iconAutoPosition[0] ++;

		foreach (['size' => 80, 'margin' => 0, 'columns' => 5] as $property => $value)
			if (!isset($this->grid[$property]))
				$this->grid[$property] = $value;

		$halfGrid = $this->grid['size'] / 2;
		return [
			'x' => intval($this->grid['margin'] + $halfGrid + (($this->iconAutoPosition[0] - 1) * $this->grid['size'])),
			'y' => intval($this->grid['margin'] + $halfGrid + ($this->iconAutoPosition[1] * $this->grid['size'])),
		];
	}

	public function getRestoreScript()
	{
		$code = '';
		foreach (['applications', 'shortcuts'] as $type)
			foreach ($this->$type as $item)
				$code .= $item->getRestoreScript();

		return $code;
	}

	/**
	 * Compacts the desktop into an array. From here you can do with it as you please
	 * @return array
	 */
	public function toData()
	{
		$data = $this->getPublicProperties();
		$data['applications'] = [];
		foreach ($this->applications as $application)
			$data['applications'][] = $application->getConfig();

		$data['shortcuts'] = [];
		foreach ($this->shortcuts as $shortcut)
			$data['shortcuts'][] = $shortcut->getConfig();

		return $data;
	}

	/**
	 * Creates a new desktop instance given the array data
	 * @param $data
	 * @return static
	 * @throws InvalidConfigException
	 */
	public static function createFromData($data)
	{
		if (!is_array($data) || !isset($data['applications']))
			throw new InvalidConfigException("Invalid data, cannot restore");

		foreach (['applications', 'shortcuts', 'menus'] as $item)
			if (isset($data[$item])) {
				$$item = $data[$item];
				unset($data[$item]);
			}

		$data['class'] = static::className();
		/** @var static $desktop */
		$desktop = \Yii::createObject($data);

		if (isset($applications))
			foreach ($applications as $application) {
				$application = \Yii::createObject($application);
				$desktop->registerApplication($application, false);
			}

		if (isset($shortcuts))
			foreach ($shortcuts as $shortcut) {
				$shortcut = \Yii::createObject($shortcut);
				$desktop->registerShortcut($shortcut);
			}

		return $desktop;
	}
}