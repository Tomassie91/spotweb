<?php
	$extList = get_loaded_extensions();
	$phpVersion = explode(".", phpversion());
	
	
	function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	} # return_bytes()
?>
<div style="margin-left:10px;">
<h1 style="font-size:22px; font-weight:bold;">Install checks</h1>
<p>When all programs are correctly installed, all boxes should be colored green.</p>
<hr/>
<div style="margin:10px; margin-left:0px;">
	<table>
		<tr> <th style="font-weight:bold;"> PHP settings </th> <th style="font-weight:bold;"> OK ? </th> </tr>
		<tr> <td> PHP version </td> <td> <?php echo ($phpVersion[0] >= '5' && $phpVersion[1] >= 3) ? "<p style='background-color:#66ff00'>OK</p>" : "PHP 5.3 or later is recommended" ?>  </td> 
</tr>
		<tr> <td> timezone settings </td> <td> <?php echo (ini_get("date.timezone")) ? "<p style='background-color:#66ff00'>OK</p>" : "Please specify date.timezone in your PHP.ini"; ?> </td> 
</tr>
		<tr> <td> Open base dir </td> <td> <?php echo (!ini_get("open_basedir")) ? "<p style='background-color:#66ff00'>OK</p>" : "Not empty, might be a problem"; ?>  </td> </tr>
		<tr> <td> PHP safe mode </td> <td> <?php echo ini_get('safe_mode') ? "Safe mode set -- will cause problems for retrieve.php" : "<p style='background-color:#66ff00'>OK</p>"; ?> </td> 
</tr>
		<tr> <td> Memory limit </td> <td> <?php echo return_bytes(ini_get('memory_limit')) < (32*1024*1024) ? "memory_limit below 32M" : "<p style='background-color:#66ff00'>OK</p>"; ?> </td> 
</tr>
	</table>
	
	<br>
	
	<table>
		<tr> <th style="font-weight:bold;"> PHP extension </th> <th style="font-weight:bold;"> OK ? </th> </tr>

		<tr> <td> SQLite </td> <td> <?php echo (array_search('SQLite', $extList) === false) ? "Not installed (geen probleem als MySQL geinstalleerd is)" : "<p 
style='background-color:#66ff00'>OK</p>" ?>  </td> </tr>
		<tr> <td> MySQL </td> <td> <?php echo (array_search('mysql', $extList) === false) ? "Not installed (geen probleem als sqlite3 geinstalleerd is)" : "<p 
style='background-color:#66ff00'>OK</p>" ?>  </td> </tr>
		<tr> <td> bcmath </td> <td> <?php echo (array_search('bcmath', $extList) === false) ? "Not installed" : "<p style='background-color:#66ff00'>OK</p>" ?> </td> </tr>
		<tr> <td> ctype </td> <td> <?php echo (array_search('ctype', $extList) === false) ? "Not installed" : "<p style='background-color:#66ff00'>OK</p>" ?> </td> </tr>
		<tr> <td> xml </td> <td> <?php echo (array_search('xml', $extList) === false) ? "Not installed" : "<p style='background-color:#66ff00'>OK</p>" ?> </td> </tr>
		<tr> <td> zlib </td> <td> <?php echo (array_search('zlib', $extList) === false) ? "Not installed" : "<p style='background-color:#66ff00'>OK</p>" ?> </td> </tr>
	</table>

	<br>
	
<?php

	function ownWarning($errno, $errstr) {
		$GLOBALS['iserror'] = true;
		#echo $errstr;
	} # ownWarning

	function testInclude($fname) {
		$GLOBALS['iserror'] = false;
		include($fname);
		return !($GLOBALS['iserror']);
	} # testInclude
		
	set_error_handler("ownWarning",E_WARNING);
?>

	<table>
		<tr> <th style="font-weight:bold;"> Include files  </th> <th style="font-weight:bold;"> OK ? </th> </tr>
		<tr> <td> PEAR </td> <td> <?php echo testInclude("System.php") ? "<p style='background-color:#66ff00'>OK</p>" : "PEAR cannot be found" ?> </td> </tr>
		<tr> <td> PEAR Net/NNTP </td> <td> <?php echo testInclude("Net/NNTP/Client.php") ? "<p style='background-color:#66ff00'>OK</p>" : "PEAR Net/NNTP package cannot be found" ?> </td> 
</tr>
		
	</table>
	
	<br> <br>
	
	<table>
		<tr> <th style="font-weight:bold;"> Path </th> <th style="font-weight:bold;"> PEAR found? </th> <th style="font-weight:bold;"> Net/NNTP found? </th> <tr>
		
<?php
		$arInclude = explode(":", ini_get("include_path")); 
		for($i = 0; $i < count($arInclude); $i++) {
			echo "\t\t<tr><td>" . $arInclude[$i] . "</td> <td> " . 
						(file_exists($arInclude[$i] . 'System.php') ? "<p style='background-color:#66ff00'>OK</p>" : "") . "</td> <td>" .
						(file_exists($arInclude[$i] . "Net/NNTP/Client.php") ? "<p style='background-color:#66ff00'>OK</p>" : "") . " </td> </tr>";
		} # foreach
?>  
	</table>
<br>
<table>

</table>
</div>	
<a href="?progress=fill_settings" style="" >All my settings are correct!</a>
</div>
