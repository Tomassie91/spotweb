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
$settings = simplexml2array($settingsobject);

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
# adding filters TO-DO: allow custom filters
#-----------------
$settings['filters'] = array(
        Array("Films (geen erotiek)", "images/video2.png", "cat0_d,!cat0_d11,!cat0_d23,!cat0_d24,!cat0_d25,!cat0_d26,!cat0_a5", "", array()),
        Array("Series", "images/series2.png", "cat0_d11", "", array()),
        Array("Boeken", "images/books2.png", "cat0_a5", "", array()),
        Array("Muziek", "images/audio2.png", "cat1", "",
                Array(
                        Array("Compressed", "images/audio2.png", "cat1_a0,cat1_a3,cat1_a5,cat1_a6", ""),
                        Array("Lossless", "images/audio2.png", "cat1_a2,cat1_a4,cat1_a7,cat1_a8", "")
                )
        ),
        Array("Spellen", "images/games2.png", "cat2", "", array()),
        Array("Applicaties", "images/applications2.png", "cat3", "", array()),
        Array("Erotiek", "images/x2.png", "cat0_d23,cat0_d24,cat0_d25,cat0_d26", "", array()),
        Array("Reset filters", "images/custom2.png", "", "", array())

        # Uncomment onderstaande als voorbeeld van een custom filter
        # ,Array("Lossless MJ", "images/audio2.png", "cat1_a2,cat1_a4,cat1_a7,cat1_a8&search[type]=Titel&search[text]=Michael+Jackson", "", array())
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
if (empty($settings['nntp_hdr']['host'])) {
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
