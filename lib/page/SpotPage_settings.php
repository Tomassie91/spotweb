<?php
require_once "lib/page/SpotPage_Abs.php";


class SpotPage_settings extends SpotPage_Abs {

	function __construct($db, $settings, $prefs) {
		parent::__construct(null, $settings, null);
	} # ctor

	function render() {
		# zet de page title
		$this->_pageTitle = "Settings";
		
		#- display stuff -#
		$this->template('header');

		if(isset($_GET['progress'])){
			require_once('lib/SpotSettings.php');
			$settingsgen = new SpotSettings();

			try {
				$settingsgen->updateXML($_POST);	
				$this->template('settings');

				//since the fancybox blindly follows links, we can insert some JS here!
	                        echo '<script>parent.window.location.reload();</script>';

			} catch(Exception $x) {
				$exmessage = $x->getMessage();
				$this->template('settings', array('exceptionmessage' => $exmessage));
			}
		} else {
			$this->template('header');
                        $this->template('settings');
		}

	$this->template('footer');

	} # render()
	
} # class SpotPage_settings
