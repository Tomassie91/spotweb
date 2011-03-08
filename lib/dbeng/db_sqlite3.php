<?php
# a mess
require_once "lib/dbeng/db_abs.php";

class db_sqlite3 extends db_abs {
	private $_db_path;
	private $_conn;
	
	function __construct($path) {
		$this->_db_path = $path;
    } # ctor

	function connect() {
		$this->_conn = @sqlite_factory($this->_db_path);
		if ($this->_conn === null) {
			throw new Exception("Unable to connect to database: " . sqlite_error_string($this->_conn->lastError()));
		} # if
		
		$this->createDatabase();
	} # connect()
	
	function safe($s) {
		return sqlite_escape_string($s);
	} # safe

	function rawExec($s) {
		$errorMsg = '';
		$tmpRes = @$this->_conn->unbufferedQuery($s, SQLITE_BOTH, $errorMsg);
		if ($tmpRes === false) {
			if (empty($errorMsg)) {
				$errorMsg =  sqlite_error_string($this->_conn->lastError());
			} # if
			throw new Exception("Error executing query: " . $errorMsg);
		} # if

		return $tmpRes;		
	} # rawExec
	
	function singleQuery($s, $p = array()) {
		# We gebruiken niet meer de 'native' singleQuery() omdat de SQL syntax errors
		# daar niet naar  boven komen
		$res = $this->exec($s, $p);
		$row = $res->fetch();

		unset($res);
		return $row[0];
	} # singleQuery

	function arrayQuery($s, $p = array()) {
		# We gebruiken niet meer de 'native' arrayQuery() omdat de SQL syntax errors
		# daar niet naar  boven komen
		$rows = array();

		$res = $this->exec($s, $p); 
		while ($rows[] = $res->fetch());

		# remove last element (false element)
		array_pop($rows); 
		
		unset($res);
		return $rows;
	} # arrayQuery

	function rows() {
		return $this->_conn->changes();
	} # rows()
	
	function createDatabase() {
		$q = $this->arrayQuery("PRAGMA table_info(spots)");
		if (empty($q)) {
			$this->rawExec("CREATE TABLE spots(id INTEGER PRIMARY KEY ASC, 
											messageid TEXT,
											spotid INTEGER,
											category INTEGER, 
											subcat INTEGER,
											poster TEXT,
											groupname TEXT,
											subcata TEXT,
											subcatb TEXT,
											subcatc TEXT,
											subcatd TEXT,
											title TEXT,
											tag TEXT,
											stamp INTEGER);");
			$this->rawExec("CREATE TABLE nntp(server TEXT PRIMARY KEY,
										maxarticleid INTEGER UNIQUE,
										nowrunning INTEGER DEFAULT 0);");
			
			# create indices
			$this->rawExec("CREATE INDEX idx_spots_1 ON spots(id, category, subcata, subcatd, stamp DESC)");
			$this->rawExec("CREATE INDEX idx_spots_2 ON spots(id, category, subcatd, stamp DESC)");
			$this->rawExec("CREATE INDEX idx_spots_3 ON spots(messageid)");
		} # if
		
		$q = $this->arrayQuery("PRAGMA table_info(commentsxover)");
		if (empty($q)) {
			$this->rawExec("CREATE TABLE commentsxover(id INTEGER PRIMARY KEY ASC,
										   messageid TEXT,
										   revid INTEGER,
										   nntpref TEXT);");
			$this->rawExec("CREATE INDEX idx_commentsxover_1 ON commentsxover(nntpref, messageid)");
		} # if
		
		# Controleer of de 'nntp' tabel wel recent is, de oude versie had 2 kolommen (server,maxarticleid)
		$q = $this->arrayQuery("PRAGMA table_info(nntp)");
		if (count($q) == 2) {
			# Niet alle SQLite versies ondersteunen alter table, dus we lezen de data in, droppen de tabel en 
			# inserten de data opnieuw
			$nntpData = $this->arrayQuery("SELECT server,maxarticleid FROM nntp");
			
			# Drop de nntp table en creeer hem opnieuw
			$this->rawExec("DROP TABLE nntp");
			$this->rawExec("CREATE TABLE nntp(server TEXT PRIMARY KEY,
													maxarticleid INTEGER UNIQUE,
													nowrunning INTEGER DEFAULT 0,
													lastrun INTEGER DEFAULT 0);");
													
			foreach($nntpData as $nntp) {
				$this->exec("INSERT INTO nntp(server,maxarticleid) VALUES('%s','%s')", 
						Array($nntp['server'],
							  $nntp['maxarticleid']));
			} # foreach
		} # if
		
		# Controleer of er wel een index zit op 'spots' tabel 
		$q = $this->arrayQuery("PRAGMA index_info(idx_spots_4)");
		if (empty($q)) {
			$q = $this->arrayQuery("CREATE INDEX idx_spots_4 ON spots(stamp);");
		}# if
		
		$q = $this->arrayQuery("PRAGMA table_info(downloadlist)");
		if (empty($q)) {
			$this->rawExec("CREATE TABLE downloadlist(id INTEGER PRIMARY KEY ASC,
										   messageid VARCHAR(250),
										   stamp INTEGER);");
			$this->rawExec("CREATE INDEX idx_downloadlist_1 ON downloadlist(messageid)");
		} # if

		# Controleer of de 'nntp' tabel wel recent is, de oude versie had 2 kolommen (server,maxarticleid)
		$q = $this->arrayQuery("PRAGMA table_info(nntp)");
		if (count($q) == 3) {
			# Niet alle SQLite versies ondersteunen alter table, dus we lezen de data in, droppen de tabel en 
			# inserten de data opnieuw
			$nntpData = $this->arrayQuery("SELECT server,maxarticleid FROM nntp");
			
			# Drop de nntp table en creeer hem opnieuw
			$this->rawExec("DROP TABLE nntp");
			$this->rawExec("CREATE TABLE nntp(server TEXT PRIMARY KEY,
													maxarticleid INTEGER UNIQUE,
													nowrunning INTEGER DEFAULT 0,
													lastrun INTEGER DEFAULT 0);");
													
			foreach($nntpData as $nntp) {
				$this->exec("INSERT INTO nntp(server,maxarticleid) VALUES('%s','%s')", 
						Array($nntp['server'],
							  $nntp['maxarticleid']));
			} # foreach
		} # if

		$q = $this->arrayQuery("PRAGMA table_info(spotsfull)");
		if (empty($q)) {
			$this->rawExec("CREATE TABLE spotsfull(id INTEGER PRIMARY KEY, 
										messageid varchar(250),
										userid varchar(32),
										verified BOOLEAN,
										usersignature TEXT,
										userkey TEXT,
										xmlsignature TEXT,
										fullxml TEXT,
										filesize INTEGER);");										

			# create indices
			$this->rawExec("CREATE INDEX idx_spotsfull_1 ON spotsfull(messageid, userid)");
		} # if
		
	} # Createdatabase

} # class
