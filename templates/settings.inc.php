<?php

//checks if the XML file is present
if(!file_exists('settings.xml')) {
        //creates an empty settings array to prevent warnings
        $settings = array();
}

#---------------
# returns value if it exists, empty string if it doesnt
#---------------
function checkForValue($vararray, $settings) {
        $val = $settings;
        foreach($vararray as $index) {
                if(array_key_exists($index, $val)) {
			if(is_array($val[$index]))
				$val = $val[$index];
			else
				return $val[$index];
               	} else {
			return '';
     	       	}
	}
}
?>

<div style="margin-left:10px;">
	<h1 style="font-size:22px; font-weight:bold;">Settings</h1>
	<p>fill in your settings</p>
	<hr/>
	<?php
	if($_GET['page'] == 'settings'){
                echo  '<form method="post" action="?page=settings&progress=done">';
	} else {
		echo  '<form method="post" action="?progress=done">';
	}
	?>
		<div style="margin-top:10px;">
		<?php if(isset($params['exceptionmessage'])){ ?>
		<p style="border:1px solid; border-color:red;"><?php echo $params['exceptionmessage']; ?></p>
		<?php } ?>
			<p style="font-weight:bold;">NNTP(usenet) settings:</p>
			<p>NNTP host: <input type="text" name="NNTP_host" value="<?php echo checkForValue(array('nntp_nzb', 'host'), $settings); ?>"></p>
			<p>NNTP username: <input type="text" name="NNTP_username" value="<?php echo checkForValue(array('nntp_nzb', 'user'), $settings); ?>"></p>
			<p>NNTP password: <input type="password" name="NNTP_password"  value="<?php echo checkForValue(array('nntp_nzb', 'pass'), $settings); ?>"></p>
			<p>NNTP port: <input type="text" name="NNTP_port" value="119"  value="<?php echo checkForValue(array('nntp_nzb', 'port'), $settings); ?>"></p>
			<?php
				 $enc = checkForValue(array('nntp_nzb', 'enc'), $settings);
			?>
			<p>NNTP encryption: <select name="NNTP_enc"><option value="false" <?php if($enc == 'false'){echo 'selected="selected"';}?>>none</option><option value="SSL" <?php if($enc == 'SSL'){echo 'selected="selected"';}?> >SSL</option><option value="TLS" <?php if($enc == 'TLS'){echo 'selected="selected"';}?>>TLS</option></select></p>
		</div>

		<p style="font-weight:bold;">Use seperate header server: <input type="checkbox" name="use_header_NNTP" value="true" id="use_header_NNTP"></p>

		<div style="display:none;" id="NNTP_header_server">
			<p style="font-weight:bold;">NNTP(usenet) header settings:</p>
			<p>NNTP header host: <input type="text" name="NNTP_header_host" value="<?php echo checkForValue(array('nntp_hdr', 'host'), $settings); ?>"></p>
                        <p>NNTP header username: <input type="text" name="NNTP_header_username" value="<?php echo checkForValue(array('nntp_hdr', 'user'), $settings); ?>"></p>
                        <p>NNTP header password: <input type="password" name="NNTP_header_password" value="<?php echo checkForValue(array('nntp_hdr', 'pass'), $settings); ?>"></p>
                        <p>NNTP header port: <input type="text" name="NNTP_header_port" value="119" value="<?php echo checkForValue(array('nntp_hdr', 'port'), $settings); ?>"></p>

			 <?php
                                 $enc = checkForValue(array('nntp_hdr', 'enc'), $settings);
                        ?>

                        <p>NNTP encryption: <select name="NNTP_header_enc"><option value="false" <?php if($enc == 'false'){echo 'selected="selected"';}?>>none</option><option value="SSL" <?php if($enc == 'SSL'){echo 'selected="selected"';}?> >SSL</option><option value="TLS" <?php if($enc == 'TLS'){echo 'selected="selected"';}?>>TLS</option></select></p>
		</div>

		<div style="margin-top:10px;">
			<p style="font-weight:bold;">Database settings:</p>
			<p>Database type: <select name="DB_type" id="DB_type"><option value="sqlite3">SQLite3</option><option value="mysql">MySQL</option></select></p>

			<?php
				 $path = checkForValue(array('db', 'path'), $settings);
			?>
			<p id="SQLite" >SQLLite location: <input type="text" name="SQLLite_location" <?php if($path == ''){ echo 'value="./nntpdb.sqlite3"'; } else { echo 'value="' . $path . '"'; } ?>/></p>
		
			<div style="display:none" id="MySQL">
				<?php 
					$mysql_host = checkForValue(array('db', 'host'), $settings);
					$mysql_db = checkForValue(array('db', 'dbname'), $settings);
					$mysql_user = checkForValue(array('db', 'user'), $settings);
					$mysql_pass = checkForValue(array('db', 'pass'), $settings);
				?>
				<p>MySQL host: <input type="text" name="mysql_host" <?php if($mysql_host == ''){ echo 'value="localhost"'; } else { echo 'value="' . $mysql_host . '"'; }?>></p>
				<p>MySQL db: <input type="text" name="mysql_db" <?php if($mysql_db == ''){ echo 'value="spotweb"'; } else { echo 'value="' . $mysql_db . '"'; } ?>></p>
				<p>MySQL username: <input type="text" name="mysql_username" <?php if($mysql_user == ''){ echo 'value="spotnet"'; } else { echo 'value="' . $mysql_user . '"'; } ?>></p>
				<p>MySQL password: <input type="password" name="mysql_password" <?php if($mysql_pass == ''){ echo 'value="spotnet"'; } else { echo 'value="' . $mysql_pass . '"'; } ?>></p>
			</div>
		</div>

		<div style="margin-top:10px;">
		<p style="font-weight:bold;">SABnzbd+ settings:</p>
			<p>SABnzbd+ connection type: <select name="sab_type" id="sab_type"><option value="">None</option><option value="blackhole">Blackhole</option><option value="api">API</option></select></p>

			<div style="display:none" id="api">
				<?php
					$sab_host = checkForValue(array('sabnzbd', 'host'), $settings);
					$sab_api = checkForValue(array('sabnzbd', 'apikey'), $settings);
					$sab_url = checkForValue(array('sabnzbd', 'spotweburl'), $settings);
				?>

        	                <p>SABnzbd+ host: <input type="text" name="sab_host" <?php if($sab_host == '') { echo 'value="192.168.10.122:8081"'; } else { echo 'value="' . $sab_host . '"';} ?> /></p>
                	        <p>SABnzbd+ api-key: <input type="text" name="sab_api_key"  value="<?php echo $sab_api; ?>"></p>
                       		<p>Spotweb URL: <input type="text" name="spotweb_url" <?php if($sab_url == '') { echo 'value="http://server/spotweb"'; } else { echo 'value="' . $sab_url .'"';} ?>/></p>
	                </div>

			 <div style="display:none" id="blackhole">
				<p>SABnzbd+ blackhole location: <input type="text" name="sab_blackhole" value="/path/to/blackhole" value="<?php echo checkForValue(array('nzb_local_queue_dir'), $settings); ?>"></p>
			</div>
		</div>

		<div style="margin-top:10px;">
			<p style="font-weight:bold;">Other settings:</p>
			<?php
				$update_button =  checkForValue(array('show_updatebutton'), $settings);
				$nzb_button = checkForValue(array('show_nzbbutton'), $settings);
			?>
			<p>Show update button: <select name="show_update_button"><option value="false" <?php if($update_button == false){echo 'selected="selected"';}?>>false</option><option value="true"<?php if($update_button == true){echo 'selected="selected"';}?>>true</option></select></p>
			<p>Show NZB button: <select name="show_nzb_button"><option value="false" <?php if($nzb_button == false){echo 'selected="selected"';}?>>false</option><option value="true"<?php if($nzb_button == true){echo 'selected="selected"';}?>>true</option></select></p>
			<p>NZB button URL: <input type="text" name="search_url" value="http://www.binsearch.info/q=$SPOTFNAME" value="<?php echo checkForValue(array('search_url'), $settings); ?>" /></p>
			<p>Hide porn: <select name="hide_porn"><option value="false" <?php if($update_button == false){echo 'selected="selected"';}?>>false</option><option value="true"<?php if($update_button == true){echo 'selected="selected"';}?>>true</option></select></p>
		</div>
		<br/>
		<br/>
		<input type="submit" name="submit" value="submit" />
	</form>
</div>

<script>
$("#DB_type").change(function(){
	if($("#DB_type").val() == 'sqlite3') {
		$("#MySQL").hide();
		$("#SQLite").show();
	} else if($("#DB_type").val() == 'mysql') {
		$("#MySQL").show();
		$("#SQLite").hide();
	} else {
		$("#SQLite").hide();
		 $("#MySQL").hide();
	}
});

$("#sab_type").change(function(){
        if($("#sab_type").val() == 'api') {
                $("#blackhole").hide();
                $("#api").show();
        } else if($("#sab_type").val() == 'blackhole') {
                $("#blackhole").show();
                $("#api").hide();
        } else {
                $("#api").hide();
                 $("#blackhole").hide();
        }
});
var new_NNTP_header_state = false;

$("#use_header_NNTP").change(function(){
        if(new_NNTP_header_state == true) {
		$("#NNTP_header_server").hide();
		new_NNTP_header_state = false;
	} else {
		$("#NNTP_header_server").show();
                new_NNTP_header_state = true;
	}
});

<?php

//switching the types, using JQuery.
$db_type = checkForValue(array('db', 'engine'), $settings);
$sab_type = checkForValue(array('sabnzbd', 'apikey'), $settings);
$sab_blackhole_type = checkForValue(array('nzb_local_queue_dir'), $settings);
$nntp_header_type = checkForValue(array('nntp_hdr', 'host'), $settings);

if(isset($db_type)) {
	if($db_type == 'mysql')
		echo '$("\#DB_type").val("mysql");';
	else
		echo '$("\#DB_type").val("sqlite3");';

	echo '$("#DB_type").change();';
}

if(isset($sab_blackhole_type) && $sab_blackhole_type != '') {
	 echo '$("\#sab_type").val("blackhole");';
	 echo '$("#sab_type").change()';
}

if(isset($sab_type) && $sab_type != '') {
         echo '$("\#sab_type").val("api");';
 	 echo '$("#sab_type").change();';
}

if(isset($nntp_header_type) && $nntp_header_type != '') {
         echo '$("#use_header_NNTP").attr("checked", true);';
 	 echo '$("#NNTP_header_server").show();';
}

?>
</script>
