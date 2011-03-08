<?php
error_reporting(E_ALL & ~8192 & ~E_USER_WARNING);	# 8192 == E_DEPRECATED maar PHP < 5.3 heeft die niet

require_once "settings.php";
require_once "lib/SpotDb.php";
require_once "lib/SpotReq.php";
require_once "lib/SpotParser.php";
require_once "lib/SpotsOverview.php";
require_once "lib/SpotCategories.php";

#- main() -#
try {
	#install script
	if(!isset($settings['is_installed']) || $settings['is_installed'] == false){
        	require_once('lib/page/SpotPage_install.php');
	        $page = new SpotPage_install();
        	$page->render();
	        exit;
	}

	//NNTP include is needed for system check
	require_once "lib/SpotNntp.php";

	# database object
	$db = new SpotDb($settings['db']);
	$db->connect();

	# helper functions for passed variables
	$req = new SpotReq();
	$req->initialize();

	$page = $req->getDef('page', 'index');
	if (array_search($page, array('index', 'catsjson', 'getnzb', 'getspot', 'erasedls', 'settings')) === false) {
		$page = 'index';
	} # if

	$req->getPageRequire($page);

	switch($page) {

		case 'getspot' : {
				$page = new SpotPage_getspot($db, $settings, $settings['prefs'], $req->getDef('messageid', ''));
				$page->render();
				break;
		} # getspot

		case 'getnzb' : {
				$page = new SpotPage_getnzb($db, $settings, $settings['prefs'], $req->getDef('messageid', ''));
				$page->render();
				break;
		} # getspot

		case 'erasedls' : {
				$page = new SpotPage_erasedls($db, $settings, $settings['prefs']);
				$page->render();
				break;
		} # erasedls
                case 'settings' : {
                                $page = new SpotPage_settings($db, $settings, $settings['prefs']);
                                $page->render();
                                break;
                }
		
		case 'catsjson' : {
				$page = new SpotPage_catsjson($db, $settings, $settings['prefs']);
				$page->render();
				break;
		} # getspot

		case 'index' : {
				$page = new SpotPage_index($db, $settings, $settings['prefs'], 
							Array('search' => $req->getDef('search', $settings['index_filter']),
								  'page' => $req->getDef('page', 0),
								  'sortby' => $req->getDef('sortby', ''),
								  'sortdir' => $req->getDef('sortdir', ''))
					);
				$page->render();
				break;
		} # getspot
	} #page switch
}
catch(Exception $x) {
	die($x->getMessage());
} # catch
