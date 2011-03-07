<?php
require_once "lib/page/SpotPage_Abs.php";


class SpotPage_install extends SpotPage_Abs {

	function __construct() {
		$settings['tpl_path'] = './templates/';
		parent::__construct(null, $settings, null);
	} # ctor

	function render() {
		# zet de page title
		$this->_pageTitle = "Install script";
		
		#- display stuff -#
		$this->template('header');

		if(!isset($_GET['progress']))
			$this->template('install_check');
		elseif($_GET['progress'] == 'fill_settings')
			$this->template('install_filler');
		elseif($_GET['progress'] == 'done') {
			require_once('lib/SettingsGenerator.php');
			$settingsgen = new SettingsGenerator();
			$exmessage = '';
			try {
				$settingsgen->createXML($_POST);
	                        $this->template('install_done');
			} catch(Exception $x) {
				$exmessage = $x->getMessage();
				$this->template('install_filler', array('exceptionmessage' => $exmessage));
			}
		}

		$this->template('footer');
	} # render()
	
} # class SpotPage_index
