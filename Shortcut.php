<?php
/**
 * Represents a single desktop shortcut.
 * By default it is assumed to be an "icon" with a caption underneath. Feel free to override this and change the
 * render method for custom things.
 */

namespace bedezign\yii2\desktop;

class Shortcut
	extends components\Component
	implements DesktopEventHandler
{
	/**
	 * If you leave this on null, no label will be rendered
	 * @var string
	 */
	public $title     = null;

	/**
	 * ID of the shortcut. If you leave this empty it will be auto assigned when you link it to a desktop
	 * @var string
	 */
	public $id        = null;

	/**
	 * Attributes for the anchor. By default this just contains a 'href' = '#application_id'
	 * @var array
	 */
	public $anchor    = null;

	public $data      = null;

	/**
	 * Absolute position of the shortcut
	 * @var array['x', 'y']
	 */
	public $position  = null;

	/**
	 * X-difference to apply on top of the position for correct placement. Default value assumes 32px icons.
	 * Note that this is only active when auto positioning the shortcut. It will be zeroed out once a position event is received
	 * @var int
	 */
	public $deltaX    = - 16;

	/**
	 * Y-difference to apply on top of the position for correct placement. Default value assumes 32px icons
	 * Note that this is only active when auto positioning the shortcut. It will be zeroed out once a position event is received
	 * @var int
	 */
	public $deltaY    = - 16 ;

	public function render()
	{
		if (!$this->position)
			$this->position = $this->desktop->getShortcutPosition();

		return $this->desktop->render('_shortcut', ['shortcut' => $this,
			'x' => $this->position['x'] + $this->deltaX, 'y' => $this->position['y'] + $this->deltaY]);
	}

	/**
	 * Creates a shortcut based on the given application (same icon, title etc)
	 * @param Application $application
	 * @return static
	 */
	public static function fromApplication(Application $application)
	{
		$shortcut = new static;
		$shortcut->title  = $application->title;
		if ($application->hasIcon)
			$shortcut->icon = $application->icon;

		$shortcut->setTargetApplication($application);

		return $shortcut;
	}

	/**
	 * Update the shortcuts' target to open the given application
	 * @param Application $application
	 */
	public function setTargetApplication(Application $application)
	{
		$this->anchor = ['href' => '#' . $application->applicationId];
	}

	public function canHandleEvent($type)
	{
		return in_array($type, ['shortcut.moved']);
	}

	public function event($data)
	{
		switch ($data['action']) {
			case 'shortcut.moved' :
				if (!isset($data['left']) || !isset($data['top']))
					return false;

				$this->position = ['x' => $data['left'], 'y' => $data['top']];
				// Since we now have an actual position, we no longer need the delta's
				$this->deltaX = $this->deltaY = 0;
				return true;
		}

		return false;
	}
}