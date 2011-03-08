<?php

class SpotReq {
    static private $_merged = array(); 
    
    function initialize() {
		SpotReq::$_merged = array_merge($_POST, $_GET);
    }
    
    function get($varName, $escapeType = 'html') {
		if( is_array($varName) ) {
			return SpotReq::escape(SpotReq::$_merged[$varName[0]][$varName[1]], $escapeType);
		} else {
			return SpotReq::escape(SpotReq::$_merged[$varName], $escapeType);
		}
    }    
    
   
    function doesExist($varName) {
		if( is_array($varName) ) {
			return isset(SpotReq::$_merged[$varName[0]][$varName[1]]);
		}
		else {
			return isset(SpotReq::$_merged[$varName]);
		}
    } 
 
    function getDef($varName, $defValue, $escapeType = 'html') {
		if( !isset(SpotReq::$_merged[$varName]) ) {
			return $defValue;
		} else {
			return SpotReq::get($varName, $escapeType);
		}
    }

    function getSrvVar($varName, $defValue = '', $escapeType = 'html') {
		if( isset($_SERVER[$varName]) ) {
			return SpotReq::escape($_SERVER[$varName], $escapeType);
		} else {
			return $defValue;
		}
    }
    
    function escape($var, $escapeType) {
		if( is_array($var) ) {
			foreach($var as $key => $value) {
				$var[$key] = SpotReq::escape($value, $escapeType);
			}
    
			return $var;
		} else {
    	    // and start escaping
			switch( $escapeType ) {
				case 'html'  : return htmlspecialchars($var);
							   break;
				
				default : die('Unknown escape type: ' . $escapeType);
			} # switch
		} #else
    }

    function getPageRequire($page) {
	$requirestring = 'lib/page/SpotPage_' . $page . '.php';

	if(file_exists($requirestring))
		require_once($requirestring);
	else
		throw new Exception('Page file could not be found!');
    }
}
