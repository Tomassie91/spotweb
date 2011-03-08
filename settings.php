<?php

#--------------------
# Convert SimpleXMLElement object to array
# Copyright Daniel FAIVRE 2005 - www.geomaticien.com
# Copyleft GPL license
#--------------------
function simplexml2array($xml) {
   if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
       $attributes = $xml->attributes();
       foreach($attributes as $k=>$v) {
           if ($v) $a[$k] = (string) $v;
       }
       $x = $xml;
       $xml = get_object_vars($xml);
   }
   if (is_array($xml)) {
       if (count($xml) == 0) return (string) $x; // for CDATA
       foreach($xml as $key=>$value) {
	if($value == 'true')
		$value = true;
	elseif($value == 'false')
		$value = false;

           $r[$key] = simplexml2array($value);
       }
       if (isset($a)) $r['@'] = $a;    // Attributes
       return $r;
   }
   return (string) $xml;
}

#--------------------
# locating the settings.xml file and generating the settings array
#--------------------
if(file_exists('./settings.xml'))
	$settingsobject = simplexml_load_file('./settings.xml');
else if(file_exists('../settings.xml'))
	$settingsobject = simplexml_load_file('../settings.xml');
else
	$GLOBALS['settings']['is_installed'] = false;

#-------------------
# converting the SimpleXML Object to an array
#-------------------
if(isset($settingsobject))
	$settings = simplexml2array($settingsobject);
else
	$settings = array();

#-------------------
#defining the version
#-------------------
if(isset($settings['version']))
	define('VERSION', $settings['version']);

#-------------------
# adding elements to the settings array that don't need configuring
# ------------------
$settings['index_filter'] = array();

$settings['retrieve_increment'] = 1000;

$settings['prefs']['perpage'] = 100;

$settings['hdr_group'] = 'free.pt';
$settings['nzb_group'] = 'alt.binaries.ftd';
$settings['comment_group'] = 'free.usenet';

$settings['tpl_path'] = './templates/';

$settings['sabnzbd']['url'] = 'http://$SABNZBDHOST/sabnzbd/api?mode=addurl&amp;name=$NZBURL&amp;nzbname=$SPOTTITLE&amp;cat=$SANZBDCAT&amp;apikey=$APIKEY&amp;output=json';


#------------------
# checking porn filter
#------------------
if(isset($settings['hide_porn']) && $settings['hide_porn'] == true) {
	$settings['index_filter'] = array('cat' => array('0' => array('a!d23', 'a!d24', 'a!d25', 'a!d26')));
}

#------------------
# adding rsa keys
#------------------
$settings['rsa_keys'] = array();
$settings['rsa_keys'][2] = array('modulo' => 'ys8WSlqonQMWT8ubG0tAA2Q07P36E+CJmb875wSR1XH7IFhEi0CCwlUzNqBFhC+P',
								 'exponent' => 'AQAB');
$settings['rsa_keys'][3] = array('modulo' => 'uiyChPV23eguLAJNttC/o0nAsxXgdjtvUvidV2JL+hjNzc4Tc/PPo2JdYvsqUsat',
								 'exponent' => 'AQAB');
$settings['rsa_keys'][4] = array('modulo' => '1k6RNDVD6yBYWR6kHmwzmSud7JkNV4SMigBrs+jFgOK5Ldzwl17mKXJhl+su/GR9',
								 'exponent' => 'AQAB');
#-----------------
# adding spotnet filters
#-----------------
$settings['filters'] = array(    
    Array("Reset filters", "images/icons/home.png", "", "", array()),
    Array("Beeld", "images/icons/film.png", "cat0_d,!cat0_d11,!cat0_d23,!cat0_d24,!cat0_d25,!cat0_d26,!cat0_a5", "", 
        Array(
            Array("DivX", "images/icons/divx.png", "cat0_a0", ""),
            Array("WMV", "images/icons/wmv.png", "cat0_a1", ""),
            Array("MPEG", "images/icons/mpg.png", "cat0_a2", ""),
            Array("DVD", "images/icons/film.png", "cat0_a3,cat0_a10", ""),
            Array("HD", "images/icons/hd.png", "cat0_a4,cat0_a6,cat0_a7,cat0_a8,cat0_a9", ""),
            Array("Series", "images/icons/tv.png", "cat0_d11", ""),
            Array("Boeken", "images/icons/book.png", "cat0_a5", ""),
            Array("Erotiek", "images/icons/female.png", "cat0_d23,cat0_d24,cat0_d25,cat0_d26", "")
        )
    ),    
    Array("Muziek", "images/icons/music.png", "cat1_a", "", 
        Array(
            Array("Compressed", "images/icons/music.png", "cat1_a0,cat1_a3,cat1_a5,cat1_a6", ""),
            Array("Lossless", "images/icons/music.png", "cat1_a2,cat1_a4,cat1_a7,cat1_a8", "")
        )
    ),
    Array("Spellen", "images/icons/controller.png", "cat2_a", "", 
        Array(
            Array("Windows", "images/icons/windows.png", "cat2_a0", ""),
            Array("Mac / Linux", "images/icons/linux.png", "cat2_a1,cat2_a2", ""),
            Array("Playstation", "images/icons/playstation.png", "cat2_a3,cat2_a4,cat2_a5,cat2_a12", ""),
            Array("XBox", "images/icons/xbox.png", "cat2_a6,cat2_a7", ""),
            Array("Nintendo", "images/icons/nintendo_ds.png", "cat2_a9,cat2_a10,cat2_a11", ""),
            Array("PDA", "images/icons/phone.png", "cat2_a13", "")
        )
    ),
    Array("Applicaties", "images/icons/application.png", "cat3_a", "", 
        Array(
            Array("Windows", "images/icons/vista.png", "cat3_a0", ""),
            Array("Mac / Linux / OS2", "images/icons/linux.png", "cat3_a1,cat3_a2,cat3_a3", ""),
            Array("PDA / Navigatie", "images/icons/phone.png", "cat3_a4,cat3_a5,cat3_a6,cat3_a7", "")
        )
    )
);


#-----------------
# adding SABnzbd+ filters TO-DO: allow custom SABnzbd+ filters
#-----------------
$settings['sabnzbd']['categories'] = Array(
                0       => Array('default'      => "movies",                            # Default categorie als niets anders matched
                                         'a5'           => "books",
                                         'd2'           => "anime",
                                         'd11'          => "tv",
                                         'd29'          => "anime"),
                1       => Array('default'      => 'music'),
                2       => Array('default'      => 'games'),
                3       => Array('default'      => 'apps',
                                         'a3'           => 'consoles',
                                         'a3'           => 'consoles',
                                         'a4'           => 'consoles',
                                         'a5'           => 'consoles',
                                         'a6'           => 'consoles',
                                         'a7'           => 'consoles',
                                         'a8'           => 'consoles',
                                         'a9'           => 'consoles',
                                         'a10'          => 'consoles',
                                         'a11'          => 'consoles',
                                         'a12'          => 'consoles',
                                         'a13'          => 'pda',
                                         'a14'          => 'pda',
                                         'a15'          => 'pda')
        );

#-----------------
# other staps
#-----------------

#-----------------
# Override NNTP header/comments settings, als er geen aparte NNTP header/comments server is opgegeven, gebruik die van de NZB server
#-----------------
if (isset($settings['nntp_hdr']) && empty($settings['nntp_hdr']['host'])) {
	$settings['nntp_hdr'] = $settings['nntp_nzb'];
}


#----------------
# DEBUG: print all settings values
#----------------
/**
echo '
SETTINGS:
----------------
';
print_r($settings);
echo '
----------------
';
**/
