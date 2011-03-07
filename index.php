<?php
error_reporting(E_ALL & ~8192 & ~E_USER_WARNING);	# 8192 == E_DEPRECATED maar PHP < 5.3 heeft die niet

#settings
require_once "settings.php";
$settings = $GLOBALS['settings'];

#install script
if(!isset($GLOBALS['settings']['is_installed']) || $GLOBALS['settings']['is_installed'] == false){
	require_once('lib/page/SpotPage_install.php');
	$page = new SpotPage_install();
	$page->render();
	exit;
}

require_once "lib/SpotDb.php";
require_once "lib/SpotReq.php";
require_once "lib/SpotParser.php";
require_once "lib/SpotsOverview.php";
require_once "lib/SpotCategories.php";
require_once "lib/SpotNntp.php";
require_once "lib/page/SpotPage_index.php";
require_once "lib/page/SpotPage_getnzb.php";
require_once "lib/page/SpotPage_getspot.php";
require_once "lib/page/SpotPage_catsjson.php";

function openDb($dbSettings) {
	# fireup the database
	try {
		$db = new SpotDb($dbSettings);
		$db->connect();
	} 
	catch(Exception $x) {
		die('Unable to open database: ' . $x->getMessage());
	} # catch
	
	return $db;
} # openDb

function initialize() {
	# we define some preferences, later these could be
	# user specific or stored in a cookie or something
	$prefs = array('perpage' => 100);

	if (isset($settings['prefs'])) {
		$prefs = array_merge($prefs, $settings['prefs']);
	} # if
		
	# helper functions for passed variables
	$req = new SpotReq();
	$req->initialize();

	# gather the current page
	$GLOBALS['site']['page'] = $req->getDef('page', 'index');
	if (array_search($GLOBALS['site']['page'], array('index', 'catsjson', 'getnzb', 'getspot')) === false) {
		$GLOBALS['site']['page'] = 'index';
	} # if
	
	# and put them in an encompassing site object
	$GLOBALS['site']['req'] = $req;
	$settings = $GLOBALS['settings'];
	$GLOBALS['site']['settings'] = $settings;
	$GLOBALS['site']['prefs'] = $prefs;
	$GLOBALS['site']['pagetitle'] = 'SpotWeb - ';
	$GLOBALS['site']['db'] = openDb($settings['db']);
} # initialize()

#- main() -#
try {
	initialize();

	extract($GLOBALS['site'], EXTR_REFS);

	switch($site['page']) {
		case 'getspot' : {
				$page = new SpotPage_getspot($db, $settings, $prefs, $req->getDef('messageid', ''));
				$page->render();
				break;
		} # getspot

		case 'getnzb' : {
				$page = new SpotPage_getnzb($db, $settings, $prefs, $req->getDef('messageid', ''));
				$page->render();
				break;
		} # getspot

		case 'catsjson' : {
				$page = new SpotPage_catsjson($db, $settings, $prefs);
				$page->render();
				break;
		} # getspot

		case 'index' : {
				$page = new SpotPage_index($db, $settings, $prefs, 
							Array('search' => $req->getDef('search', $settings['index_filter']),
								  'page' => $req->getDef('page', 0))
					);
				$page->render();
				break;
		} # getspot
	} # switch
}
catch(Exception $x) {
	die($x->getMessage());
} # catch
