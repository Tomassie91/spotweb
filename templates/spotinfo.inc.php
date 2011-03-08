			<div class="spotinfocontainer">
				<br>
				<br>
<?php
	if (!$spot['verified']) {
?>
					<div class='warning'>
						Spot is niet geverifieerd!
					</div>
<?php
	}
?>

<?php
	# fix up the category number
	$hcat = ((int) $spot['category']);

	# fix the sabnzbdurl en searchurl
	$spot['sabnzbdurl'] = $tplHelper->makeSabnzbdUrl($spot);
	$spot['searchurl'] = $tplHelper->makeSearchUrl($spot);
	
	# and display the image and website
	if (!empty($spot['website'])) {
		echo "\t\t\t\t" . '<a href="' . htmlentities($spot['website']) . '"><img class="spotinfoimage" src="' . htmlentities($spot['image']) . '"></a>';
	} elseif (!empty($spot['image'])) {
		echo "\t\t\t\t" . '<img class="spotinfoimage" src="' . htmlentities($spot['image']) . '">';
	} # else

	# display the download button
	if (!empty($spot['segment'])) {
		if (!empty($spot['sabnzbdurl'])) {
			echo "\t\t\t\t<a href='" . $spot['sabnzbdurl'] . "' target='_blank'><img style='float: right;' src='images/download3.png' class='sabnzbd-button'></a>";
		} else {
			echo "\t\t\t\t<a href='?page=getnzb&amp;messageid=" . $spot['messageid'] . "'><img style='float: right;' src='images/download3.png'></a>";
		}
	} else {
		echo "\t\t\t\t<a href='" . $spot['searchurl']. "'><img style='float: right;' src='images/download3.png'></a>";
	} # if

	# and fixup the description text
	$tmp = $tplHelper->formatDescription($spot['description']);
	echo "<pre>$tmp</pre>";
?>
	
				<br class="spotinfoclear">

				<table class="spotinfotable">
					<tr> <th> Categorie </th> <td> <?php echo SpotCategories::HeadCat2Desc($hcat); ?> </td> </tr>
		
<?php
	if (!empty($spot['subcatlist'])) {
		foreach($spot['subcatlist'] as $sub) {
			$subcatType = substr($sub, 0, 1);
			echo "\t\t\t\t\t<tr> <th> " . SpotCategories::SubcatDescription($hcat, $subcatType) .  "</th> <td> " . SpotCategories::Cat2Desc($hcat, $sub) . " </td> </tr>\r\n";
		} # foreach
	} # if
?>	
					<tr> <th> Omvang </th> <td> <?php echo $tplHelper->format_size($spot['size']); ?> </td> </tr>
	
					<tr> <td colspan="2"> &nbsp;  </td> </tr>
		
<?php
	if (!empty($spot['website'])) {
		echo "\t\t\t\t<tr> <th> Website </th> <td> <a href='" . $spot['website'] . "'>" . $spot['website'] . "</a> </td> </tr>";
	}
?>
					<tr> <td colspan="2"> &nbsp;  </td> </tr>
					<tr> <th> Afzender </th> <td> <?php echo $spot['poster']; ?> (<?php echo $spot['userid']; ?>)</td> </tr>
					<tr> <th> Tag </th> <td> <?php echo $spot['tag']; ?> </td> </tr>

					<tr> <td colspan="2"> &nbsp;  </td> </tr>
					<tr> <th> Zoekmachine </th> <td> <a href='<?php echo $spot['searchurl']; ?>'>Zoek</a> </td> </tr>
<?php					
	if (!empty($spot['segment'])) {
?>
					<tr> <th> NZB </th> <td> <a href='?page=getnzb&amp;messageid=<?php echo $spot['messageid']; ?>'>NZB</a> </td> </tr>
<?php
	} # if
?>
<?php					
	if ((!empty($spot['sabnzbdurl'])) && (!empty($spot['segment']))) {
?>
					<tr> <th> SABnzbd </th? <td> <a href='<?php echo $spot['sabnzbdurl']; ?>' target='_blank'><?php echo utf8_encode($spot['title']); ?></a> </td> </tr>
<?php
	} # if
?>

				</table>
			
				<br class="spotinfoclear">
				<br> 
				<br>
				<br>

<?php
	if (!empty($comments)) {
?>	
			<h3 class="comment">Reacties</h3>
			<ul class="comment">
<?php
		foreach($comments as $comment) {
?>
				<li> <strong> Gepost door <?php echo $comment['from']; ?> @ <?php echo $comment['date']; ?> </strong> <br>
				<?php echo join("<br>", $comment['body']); ?>
				<br><br>
				</li>
<?php	
		} # foreach
?>
			</ul>
<?php
	} # if
?>
				<br class="spotinfoclear">
				<br> 
				<br> 
	
			</div>
			</div>
