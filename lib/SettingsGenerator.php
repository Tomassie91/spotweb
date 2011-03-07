<?php
require_once('ReadableSimpleXML.php');

class SettingsGenerator {

        public function __construct() {

        }

	public function createXML($post) {
		if(!isset($post))
			throw new Exception('no post data supplied');

		//just form validation for now, can include SQLite, MySQL, NNTP, SABnzbd+ connection tests
		$this->formValidation($post);

		return $this->saveSettings($this->createXMLFromPost($post));
		
	}

	public function saveSettings($simplexmlobject) {
		$xml = $simplexmlobject->asPrettyXML();

		file_put_contents('settings.xml',$xml);

	}

	private function formValidation($post) {
		if(!isset($post['NNTP_host'])
		|| !isset($post['NNTP_username'])
		|| !isset($post['NNTP_password'])
		|| !isset($post['NNTP_port'])
		|| !isset($post['NNTP_enc'])
		|| $post['NNTP_username'] == ''
                || $post['NNTP_password'] == ''
                || $post['NNTP_port'] == ''
                || $post['NNTP_enc'] == ''
		) {
			throw new Exception('no valid NNTP server given');
		}

		if(!isset($post['DB_type']))
			throw new Exception('no valid DB type supplied');

		if($post['DB_type'] == 'sqlite3'){
			if(!isset($post['SQLLite_location'])
			|| $post['SQLLite_location'] == ''
			) {
				throw new Exception('no valid SQLLite location supplied');
			}
		} elseif($post['DB_type'] == 'mysql') {
			if(!isset($post['mysql_host'])
			|| !isset($post['mysql_username'])
			|| !isset($post['mysql_password'])
			|| !isset($post['mysql_db'])
        	        || $post['mysql_username'] == ''
	               	|| $post['mysql_password'] == ''
              		|| $post['mysql_db'] == ''
			) {
				throw new Exception('no valid MySQL settings supplied');
			}
		} else {
			throw new Exception('no valid DB type supplied');
		}

		if(!isset($post['sab_type']))
			throw new Exception('no valid SABnzbd+ type supplied');
		
		if($post['sab_type'] == 'api'){
			if(!isset($post['sab_host'])
                        || !isset($post['sab_api_key'])
                        || !isset($post['spotweb_url'])
                        || $post['sab_host'] == ''
                        || $post['sab_api_key'] == ''
                        || $post['spotweb_url'] == ''
			) {
				throw new Exception('no valid SABnzbd+ API information supplied');
			}
		} elseif($post['sab_type'] == 'blackhole'){
			if(!isset($post['sab_blackhole']) || $post['sab_host'] == '')
				throw new Exception('no valid SABnzbd+ blackhole location supplied');
		}

		 if(!isset($post['show_update_button'])
                 || !isset($post['show_nzb_button'])
                 || !isset($post['search_url'])
                 || !isset($post['hide_porn'])
                 || $post['show_update_button'] == ''
                 || $post['show_nzb_button'] == ''
                 || $post['search_url'] == ''
                 || $post['hide_porn'] == ''
		 ) {
			throw new Exception('no valid other info supplied');
		}
		$post['search_url'] = preg_replace('/&(?!\w+;)/', '&amp;', $post['search_url']);
	}

	private function createXMLFromPost($post) {
		$defaultxml = '<?xml version="1.0" encoding="UTF-8"?><settings></settings>';
		$xml = new ReadableXMLElement($defaultxml);
		$root = $xml;

		$root->addChild('version', '0.3a');
		$root->addChild('is_installed', 'true');

		$nntp = $root->addChild('nntp_nzb');
		$nntp->addChild('host', $post['NNTP_host']);
                $nntp->addChild('user', $post['NNTP_username']);
                $nntp->addChild('pass', $post['NNTP_password']);
                $nntp->addChild('enc', $post['NNTP_enc']);
                $nntp->addChild('port', $post['NNTP_port']);

		$db = $root->addChild('db');
		$db->addChild('engine', $post['DB_type']);

		if($post['DB_type'] == 'SQLite3'){
			$db->addChild('path', $post['SQLLite_location']);
		} else {
                        $db->addChild('host', $post['mysql_host']);
                        $db->addChild('dbname', $post['mysql_db']);
                        $db->addChild('user', $post['mysql_username']);
                        $db->addChild('pass', $post['mysql_password']);
		}

                if($post['sab_type'] == 'api'){
	                $sab = $root->addChild('sabnzbd', '');
                        $sab->addChild('host', $post['sab_host']);
                        $sab->addChild('apikey', $post['sab_api_key']);
                        $sab->addChild('spotweburl', $post['spotweb_url']);
		} elseif($post['sab_type'] == 'blackhole'){
			$root->addChild('nzb_download_local', 'true');
                        $root->addChild('nzb_local_queue_dir', $post['sab_blackhole']);
		}

		$root->addChild('tpl_path', './templates');
                $root->addChild('show_updatebutton', $post['show_update_button']);
                $root->addChild('show_nzbbutton', $post['show_nzb_button']);
                $root->addChild('hide_porn', $post['hide_porn']);
                $root->addChild('search_url', $post['search_url']);

		return $xml;
	}
}
