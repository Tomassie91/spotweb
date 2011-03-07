<div style="margin-left:10px;">
	<h1 style="font-size:22px; font-weight:bold;">Settings<h1>
	<p>fill in your settings</p>
	<hr/>
	<form method="post" action="?progress=done">
		<div style="margin-top:10px;">
		<?php if(isset($params['exceptionmessage'])){ ?>
		<p style="border:1px solid; border-color:red;"><?php echo $params['exceptionmessage']; ?></p>
		<?php } ?>
			<p style="font-weight:bold;">NNTP(usenet) settings:</p>
			<p>NNTP host: <input type="text" name="NNTP_host"></p>
			<p>NNTP username: <input type="text" name="NNTP_username"></p>
			<p>NNTP password: <input type="password" name="NNTP_password"></p>
			<p>NNTP port: <input type="text" name="NNTP_port" value="119"></p>
			<p>NNTP encryption: <select name="NNTP_enc"><option value="false">none</option><option value="SSL">SSL</option><option value="TLS">TLS</option></select></p>
		</div>

		<div style="margin-top:10px;">
			<p style="font-weight:bold;">Database settings:</p>
			<p>Database type: <select name="DB_type" id="DB_type"><option value="sqlite3">SQLite3</option><option value="mysql">MySQL</option></select></p>
			
			<p id="SQLite" >SQLLite location: <input type="text" name="SQLLite_location" value="./nntpdb.sqlite3"></p>
		
			<div style="display:none" id="MySQL">
				<p>MySQL host: <input type="text" name="mysql_host" value="localhost"></p>
				<p>MySQL db: <input type="text" name="mysql_db" value="spotweb"></p>
				<p>MySQL username: <input type="text" name="mysql_username" value="spotweb"></p>
				<p>MySQL password: <input type="password" name="mysql_password" value="spotweb"></p>
			</div>
		</div>

		<div style="margin-top:10px;">
		<p style="font-weight:bold;">SABnzbd+ settings:</p>
			<p>SABnzbd+ connection type: <select name="sab_type" id="sab_type"><option value="">None</option><option value="blackhole">Blackhole</option><option value="api">API</option></select></p>

			<div style="display:none" id="api">
        	                <p>SABnzbd+ host: <input type="text" name="sab_host" value="192.168.10.122:8081"></p>
                	        <p>SABnzbd+ api-key: <input type="text" name="sab_api_key" value=""></p>
                       		<p>Spotweb URL: <input type="text" name="spotweb_url" value="http://server/spotweb"></p>
	                </div>

			 <div style="display:none" id="blackhole">
				<p>SABnzbd+ blackhole location: <input type="text" name="sab_blackhole" value="/path/to/blackhole"></p>
			</div>
		</div>

		<div style="margin-top:10px;">
			<p style="font-weight:bold;">Other settings:</p>
			<p>Show update button: <select name="show_update_button"><option value="false">false</option><option value="true">true</option></select></p>
			<p>Show NZB button: <select name="show_nzb_button"><option value="false">false</option><option value="true">true</option></select></p>
			<p>NZB button URL: <input type="text" name="search_url" value="http://www.binsearch.info/q=$SPOTFNAME"></p>
			<p>Hide porn: <select name="hide_porn"><option value="false">false</option><option value="true">true</option></select></p>
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

</script>
