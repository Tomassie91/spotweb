<div style="margin-left:10px;">
<h1 style="font-size:22px; font-weight:bold;">Succesfull installed!</h1>
<p>start using the website in a minute!</p>
<hr/>
<h2 style="font-weight:bold; font-size:16px;">Cronjob</h2>
<p>Of course it's possible to update the database manually, but it's also possible to do this via a cronjob (POSIX only). In /etc/crontab add the following line for an update every 30 minutes:</p>
<p style="margin:10px;">*/30 * * * * root cd /dir/to/spotweb && /dir/to/php retrieve.php > /dev/null</p>

<h2 style="font-weight:bold; font-size:16px;">XML file</h2>
<p>It might be handy to backup the XML file that this install script just created.</p>

<h2 style="font-weight:bold; font-size:16px;">Something go wrong?</h2>
<p>You can always delete the settings.xml file in the root of spotnet and this installscript will popup again!</p>

<a href="?">Goto Spotnet!</a>

</div>
