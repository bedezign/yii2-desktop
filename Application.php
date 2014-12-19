<?php
/**
 *
 */


namespace bedezign\yii2\desktop;

use yii\base\InvalidConfigException;
use yii\helpers\Html;

class Application
	extends components\Component
	implements DesktopEventHandler
{
	/**
	 * Unique ID for this application. Used for serializing
	 * @var string
	 */
	public $id = null;

	/**
	 * Application title
	 * @var string
	 */
	public $title = 'Application';

	/**
	 * If this application is via an IFrame, this is the application route for that frame
	 * @var string
	 */
	public $route = null;

	/**
	 * true if the application was running/should be running on desktop boot
	 * @var bool
	 */
	public $launched = false;

	/**
	 * Positional data of the application window
	 * @var string
	 */
	public $windowState = null;

	public $windowMaximised = false;

	/**
	 * Stored session data for the application (if any). This is available in the desktop by calling desktop.session_data()
	 * @var mixed
	 */
	public $sessionData = null;

	/**
	 * Application icon URL. If empty, the default icon is used.
	 * @var Icon
	 */
	protected $_icon = null;


	public function init()
	{
		if (!$this->id)
			throw new InvalidConfigException('"id" is required');

		parent::init();
	}

	/**
	 * @param array[int, int]        $size          array with new width and height.
	 */
	public function setSize($size)
	{
		if (!$this->windowState)
			// Nothing yet, fake a window position save first
			$this->windowState = ['top' => 'auto', 'left' => 'auto', 'bottom' => 'auto', 'right' => 'auto'];

		$this->windowState['width'] = str_replace('px', '', $size[0]) . 'px';
		$this->windowState['height'] = str_replace('px', '', $size[1]) . 'px';
	}

	/**
	 * @param array[int, int]     $position         array with new x and y position (left and top)
	 */
	public function setPosition($position)
	{
		if (!$this->windowState)
			// Nothing yet, fake a window position save first
			$this->windowState = ['bottom' => 'auto', 'right' => 'auto', 'width' => '700px', 'height' => '300px'];

		$this->windowState['left'] = str_replace('px', '', $position[0]) . 'px';
		$this->windowState['top'] = str_replace('px', '', $position[1]) . 'px';
	}

	/**
	 * Converts the ID into an internally used application ID
	 * @return string
	 */
	public function getApplicationId()
	{
		return 'application_' . Html::encode($this->id);
	}

	public static function toRegularId($applicationId)
	{
		if (substr($applicationId, 0, 12) == 'application_')
			return Html::decode(substr($applicationId, 12));

		return $applicationId;
	}

	public function renderWindow()
	{
		return $this->desktop->render('_applicationWindow', ['application' => $this]);
	}

	/**
	 * This functions renders the actual window contents. By default this is an empty iframe with a data-url attribute.
	 * The javascript class looks for an iframe and will change its location to the specified url if found.
	 * You can override this function if you need to output literal HTML
	 *
	 * @return string
	 */
	public function renderContent()
	{
		return $this->desktop->render('_applicationWindowContent', ['application' => $this]);
	}

	/**
	 * Return the rendered dock button.
	 * This element ties the application window to an anchor that is used to control it.
	 *
	 * @return string
	 */
	public function renderDockButton()
	{
		$id = $this->applicationId;
		$icon = $this->getIcon()->render(Icon::DISPLAY_DOCK);
		$title = $this->title;
		return <<<HTML
<li id="icon_dock_{$id}">
	<a href="#window_{$id}" id="{$id}" class="application_dock_button">
		$icon <span class="title">$title</span>
	</a>
</li>
HTML;
	}

	public function canHandleEvent($type)
	{
		return in_array($type, ['application.launched', 'application.closed', 'application.session-updated', 'window.changed']);
	}

	public function event($data)
	{
		$action = $data['action'];
		unset($data['action'], $data['application']);

		switch ($action) {
			case 'application.launched' :
				$this->launched = true;
				return true;

			case 'application.closed' :
				$this->launched = false;
				return true;

			case 'application.session-updated' :
				$this->sessionData = $data['session'];
				return true;

			case 'window.changed' :
				$this->windowMaximised = isset($data['maximized']) && $data['maximized'] == 'true';
				unset($data['maximized']);
				$this->windowState = $data;
				return true;
		}

		return false;
	}
}