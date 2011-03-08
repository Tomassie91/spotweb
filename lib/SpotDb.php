<?php
/*
 * A mess
 */
require_once "lib/dbeng/db_sqlite3.php";
require_once "lib/dbeng/db_mysql.php";

class SpotDb
{
	private $_dbsettings = null;
	private $_conn = null;
	
    function __construct($db)
    {
		global $settings;		
		$this->_dbsettings = $db;
	} # __ctor

	/*
	 * Open connectie naar de database (basically factory), de 'engine' wordt uit de 
	 * settings gehaald die mee worden gegeven in de ctor.
	 */
	function connect() {
		switch ($this->_dbsettings['engine']) {
			case 'sqlite3'	: $this->_conn = new db_sqlite3($this->_dbsettings['path']);
							  break;
							  
			case 'mysql'	: $this->_conn = new db_mysql($this->_dbsettings['host'],
												$this->_dbsettings['user'],
												$this->_dbsettings['pass'],
												$this->_dbsettings['dbname']); 
							  break;
							  
		    default			: throw new Exception('Unknown DB engine specified, please choose either sqlite3 or mysql');
		} # switch
		
		$this->_conn->connect();
    } # ctor
	
	/* 
	 * Update of insert the maximum article id in de database.
	 */
	function setMaxArticleId($server, $maxarticleid) {
		# Replace INTO reset de kolommen die we niet updaten naar 0 en dat is stom
		$res = $this->_conn->exec("UPDATE nntp SET maxarticleid = '%s' WHERE server = '%s'", Array((int) $maxarticleid, $server));
		
		if ($this->_conn->rows() == 0) {	
			$this->_conn->exec("INSERT INTO nntp(server, maxarticleid) VALUES('%s', '%s')", Array($server, (int) $maxarticleid));
		} # if
	} # setMaxArticleId()

	/*
	 * Vraag het huidige articleid (van de NNTP server) op, als die nog 
	 * niet bestaat, voeg dan een nieuw record toe en zet die op 0
	 */
	function getMaxArticleId($server) {
		$artId = $this->_conn->singleQuery("SELECT maxarticleid FROM nntp WHERE server = '%s'", Array($server));
		if ($artId == null) {
			$this->setMaxArticleId($server, 0);
			$artId = 0;
		} # if
		
		return $artId;
	} # getMaxArticleId

	/*
	 * Geef terug of de huidige nntp server al bezig is volgens onze eigen database
	 */
	function isRetrieverRunning($server) {
		$artId = $this->_conn->singleQuery("SELECT nowrunning FROM nntp WHERE server = '%s'", Array($server));
		return ((!empty($artId)) && ($artId > (time() - 3000)));
	} # isRetrieverRunning

	/*
	 * Geef terug of de huidige nntp server al bezig is volgens onze eigen database
	 */
	function setRetrieverRunning($server, $isRunning) {
		if ($isRunning) {
			$runTime = time();
		} else {
			$runTime = 0;
		} # if
		
		# Replace INTO reset de kolommen die we niet updaten naar 0 en dat is stom
		$res = $this->_conn->exec("UPDATE nntp SET nowrunning = '%s' WHERE server = '%s'", Array((int) $runTime, $server));
		if ($this->_conn->rows() == 0) {	
			$this->_conn->exec("INSERT INTO nntp(server, nowrunning) VALUES('%s', '%s')", Array($server, (int) $runTime));
		} # if
	} # setRetrieverRunning

	/*
	 * Zet de tijd/datum wanneer retrieve voor het laatst geupdate heeft
	 */
	function setLastUpdate($server) {
		return $this->_conn->exec("UPDATE nntp SET lastrun = '%d' WHERE server = '%s'", Array(time(), $server));
	} # getLastUpdate

	/*
	 * Geef de datum van de laatste update terug
	 */
	function getLastUpdate($server) {
		return $this->_conn->singleQuery("SELECT lastrun FROM nntp WHERE server = '%s'", Array($server));
	} # getLastUpdate
	
	/**
	 * Geef het aantal spots terug dat er op dit moment in de db zit
	 */
	function getSpotCount() {
		$cnt = $this->_conn->singleQuery("SELECT COUNT(1) FROM spots");
		
		if ($cnt == null) {
			return 0;
		} else {
			return $cnt;
		} # if
	} # getSpotCount

	/*
	 * Geef alle spots terug in de database die aan $sqlFilter voldoen.
	 * 
	 */
	function getSpots($pageNr, $limit, $sqlFilter, $sort) {
		$results = array();
		$offset = (int) $pageNr * (int) $limit;

		if (!empty($sqlFilter)) {
			$sqlFilter = ' WHERE ' . $sqlFilter;
		} # if
										 
 		return $this->_conn->arrayQuery("SELECT s.id AS id,
												s.messageid AS messageid,
												s.spotid AS spotid,
												s.category AS category,
												s.subcat AS subcat,
												s.poster AS poster,
												s.groupname AS groupname,
												s.subcata AS subcata,
												s.subcatb AS subcatb,
												s.subcatc AS subcatc,
												s.subcatd AS subcatd,
												s.title AS title,
												s.tag AS tag,
												s.stamp AS stamp,
												f.userid AS userid,
												f.verified AS verified,
												f.filesize AS filesize												
										 FROM spots AS s 
										 LEFT JOIN spotsfull AS f ON s.messageid = f.messageid
										 " . $sqlFilter . " 
										 ORDER BY s." . $sort['field'] . " " . $sort['direction'] . " LIMIT " . (int) $limit ." OFFSET " . (int) $offset);
	} # getSpots()

	/*
	 * Vraag 1 specifieke spot op
	 */
	function getFullSpot($messageId) {
		$tmpArray = $this->_conn->arrayQuery("SELECT s.*,
												s.spotid AS id,
												f.messageid AS messageid,
												f.userid AS userid,
												f.verified AS verified,
												f.usersignature AS \"user-signature\",
												f.userkey AS \"user-key\",
												f.xmlsignature AS \"xml-signature\",
												f.fullxml AS xml,
												f.filesize AS filesize
												FROM spots AS s 
												JOIN spotsfull AS f ON f.messageid = s.messageid 
										  WHERE s.messageid = '%s'", Array($messageId));
		if (empty($tmpArray)) {
			return ;
		} # if
		$tmpArray = $tmpArray[0];
		
		# If spot is fully stored in db and is of the new type, we process it to
		# make it exactly the same as when retrieved using NNTP
		if (!empty($tmpArray['xml']) && (!empty($tmpArray['user-signature']))) {
			$tmpArray['user-signature'] = base64_decode($tmpArray['user-signature']);
			$tmpArray['user-key'] = unserialize(base64_decode($tmpArray['user-key']));
		} # if
		
		return $tmpArray;		
	} # getFullSpot()

	
	/*
	 * Insert commentreg, 
	 *   messageid is het werkelijke commentaar id
	 *   nntpref is de id van de spot
	 *   revid is een of ander revisie nummer of iets dergelijks
	 */
	function addCommentRef($messageid, $revid, $nntpref) {
		$this->_conn->exec("INSERT INTO commentsxover(messageid, revid, nntpref) VALUES('%s', %d, '%s')",
								Array($messageid, (int) $revid, $nntpref));
	} # addCommentRef
	
	/*
	 * Geef al het commentaar voor een specifieke spot terug
	 */
	function getCommentRef($nntpref) {
		return $this->_conn->arrayQuery("SELECT messageid, MAX(revid) FROM commentsxover WHERE nntpref = '%s' GROUP BY messageid", Array($nntpref));
	} # getCommentRef

	/*
	 * Voeg een spot toe aan de lijst van gedownloade files
	 */
	function addDownload($messageid) {
		$this->_conn->exec("INSERT INTO downloadlist(messageid, stamp) VALUES('%s', '%d')",
								Array($messageid, time()));
	} # addDownload

	/*
	 * Is een messageid al gedownload?
	 */
	function hasBeenDownload($messageid) {
		$artId = $this->_conn->singleQuery("SELECT stamp FROM downloadlist WHERE messageid = '%s'", Array($messageid));
		return (!empty($artId));
	} # hasBeenDownload

	/*
	 * Geef een lijst terug van alle downloads
	 */
	function getDownloads() {
		return $this->_conn->arrayQuery("SELECT s.title AS title, dl.stamp AS stamp, dl.messageid AS messageid FROM downloadlist dl, spots s WHERE dl.messageid = s.messageid");
	} # getDownloads

	/*
	 * Wis de lijst met downloads
	 */
	function emptyDownloadList() {
		return $this->_conn->exec("TRUNCATE TABLE downloadlist;");
	} # emptyDownloadList()
	
	/*
	 * Voeg een spot toe aan de database
	 */
	function addSpot($spot, $fullSpot) {
		$this->_conn->exec("INSERT INTO spots(spotid, messageid, category, subcat, poster, groupname, subcata, subcatb, subcatc, subcatd, title, tag, stamp) 
				VALUES(%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				 Array($spot['ID'],
					   $spot['MessageID'],
					   $spot['Category'],
					   $spot['SubCat'],
					   $spot['Poster'],
					   $spot['GroupName'],
					   $spot['SubCatA'],
					   $spot['SubCatB'],
					   $spot['SubCatC'],
					   $spot['SubCatD'],
					   $spot['Title'],
					   $spot['Tag'],
					   $spot['Stamp']));
					   
		if (!empty($fullSpot)) {
			$this->addFullSpot($fullSpot);
		} # if
	} # addSpot()
	
	/*
	 * Voeg enkel de full spot toe aan de database, niet gebruiken zonder dat er een entry in 'spots' staat
	 * want dan komt deze spot niet in het overzicht te staan.
	 */
	function addFullSpot($fullSpot) {
		$this->_conn->exec("INSERT INTO spotsfull(messageid, userid, verified, usersignature, userkey, xmlsignature, fullxml, filesize)
				VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %d)",
				Array($fullSpot['messageid'],
					  $fullSpot['userid'],
					  $fullSpot['verified'],
					  base64_encode($fullSpot['user-signature']),
					  base64_encode(serialize($fullSpot['user-key'])),
					  $fullSpot['xml-signature'],
					  $fullSpot['xml'],
					  $fullSpot['size']));
	} # addFullSpot

	
	function beginTransaction() {
		$this->_conn->exec('BEGIN;');
	} # beginTransaction

	function abortTransaction() {
		$this->_conn->exec('ABORT;');
	} # abortTransaction
	
	function commitTransaction() {
		$this->_conn->exec('COMMIT;');
	} # commitTransaction
	
	function safe($q) {
		return $this->_conn->safe($q);
	} # safe
	
} # class db
