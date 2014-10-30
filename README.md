# Yii2 Desktop

A number of years ago Nathan Smith came up with a jQuery/jQuery-UI proof of concept of a sort of windows deskop  working in a browser. He called it "jqDesktop".

I really liked this and can imagine it as a very nice tool for the admin pages of a site for example.

I decided to try to link this to my Framework of choice for a very easy usage.

There's still a lot of work to be done, but so far it is already usable.
All you have to do is create a Desktop instance and feed it a number of Application instances. By default it will create icons on the desktop for them.

Every Application is linked to a url. When the application is started, it will load the actual url via an iframe.

If you supply the Desktop instance with an eventUrl, the javascript part will postback all desktop related changes back to that url. 

If you combine this with the fact that the Desktop has the ability to serialize and restore itself into/from an array of data and you can do nice things with this.

## To do

A lot of things, obviously. 
The original PoC contained things like a background, a menu and so on. My idea here is to implement MacOS like functionality.


## Usage

A possible (very basic) setup:

	public function actionIndex()
	{
		echo $this->loadDesktop()->run();
	}
	
	public function actionDesktopEvent() 
	{
		$desktop = $this->loadDesktop();
		if ($desktop->event())
			$_SESSION['desktop'] = serialize($desktop->toData());
	}
	
    public function loadDesktop() 
    {
    	if (!isset($_SESSION['desktop'])) {
        	// No saved version yet, create the desktop
        	$desktop = \Yii::createObject(['class' => '\bedezign\yii2\desktop\Desktop', 'id' => 'admin', 'eventUrl' => \yii\helpers\Url::to(['/admin/default/desktop-event'])]);
        	$desktop->registerApplication(\Yii::createObject(['class' => '\bedezign\yii2\desktop\Application', 'id' => 'testing', 'title' => 'Test Application', 'route' => '/admin/users']));
        }
		else
			$desktop = \bedezign\yii2\desktop\Desktop::createFromData(unserialize($_SESSION['desktop']));

		return $desktop;
	}

     
And that is it. This would should you a desktop with a single icon (default image). When opened, the iframe would contain whatever your admin UsersController::actionIndex() returns.
