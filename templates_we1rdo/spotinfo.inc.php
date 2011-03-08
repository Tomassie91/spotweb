<?php
	$spot = $tplHelper->formatSpot($spot);
?>
    	<div class="details <?php echo $tplHelper->cat2color($spot['category']); ?>">
            <a class="postimage" href="<?php echo $spot['website']; ?>">
                <img class="spotinfoimage" src="<?php echo $spot['image']; ?>">
            </a>
			<div class="spotinfo">
				<table class="spotheader">
					<tbody>
                    	<tr>
<?php
	if (!$spot['verified']) {
?>
							<th class="warning">Deze Spot is niet geverifieerd, de naam van de poster is niet bevestigd</th>
<?php
	}
?>
						
                        	<th class="category"><?php echo $spot['formatname'];?></th>
                            <th class="title"><?php echo $spot['title'];?></th>
                            <th class="nzb">
                            	<a class="search" href="<?php echo $spot['searchurl'];?>" title="NZB zoeken">Zoeken</a>
                                |
<?php if (!empty($spot['segment'])) { ?>
                            	<a class="nzb" href="?page=getnzb&amp;messageid=<?php echo $spot['messageid']; ?>" title="NZB downloaden">NZB</a>
<?php } ?>								
                            </th>
<?php if ((!empty($spot['segment'])) && (!empty($spot['sabnzbdurl']))) { ?>
                            <th class="sabnzbd"><a href="<?php echo $spot['sabnzbdurl'];?>" title="Add NZB to SabNZBd queue"><img height="16" width="16" src="images/download-small.png" class="sabnzbd-button"></a></th>
<?php } ?>								
                        </tr>
                    </table>
                </table>
                
				<table class="spotinfo">
                	<tbody>
                        <tr><th> Categorie </th> <td> <?php echo $spot['catname']; ?> </td> </tr>
<?php
	if (!empty($spot['subcatlist'])) {
		foreach($spot['subcatlist'] as $sub) {
			$subcatType = substr($sub, 0, 1);
			echo "\t\t\t\t\t\t<tr><th> " . SpotCategories::SubcatDescription($spot['category'], $subcatType) .  "</th> <td> " . SpotCategories::Cat2Desc($spot['category'], $sub) . " </td> </tr>\r\n";
		} # foreach
	} # if
?>
                        <tr><th> Omvang </th> <td> <?php echo $tplHelper->format_size($spot['size']); ?> </td> </tr>
                        <tr><td class="break" colspan="2">&nbsp;   </td> </tr>
                        <tr><th> Website </th> <td> <a href='<?php echo $spot['website']; ?>' target="_blank"><?php echo $spot['website'];?></a> </td> </tr>
                        <tr> <td class="break" colspan="2">&nbsp;   </td> </tr>
                        <tr> <th> Afzender </th> <td> <?php echo $spot['poster']; ?> (<?php echo $spot['userid']; ?>) </td> </tr>
                        <tr> <th> Tag </th> <td> <?php echo $spot['tag']; ?> </td> </tr>
                        <tr> <td class="break" colspan="2">&nbsp;   </td> </tr>
                        <tr> <th> Zoekmachine </th> <td> <a href='<?php echo $spot['searchurl']; ?>'>Zoek</a> </td> </tr>
                        <tr> <th> NZB </th> <td> <a href='?page=getnzb&amp;messageid=<?php echo $spot['messageid']; ?>'>NZB</a> </td> </tr>
                    </tbody>
				</table>
      		</div>
            <div class="description">
            	<h4>Post Description (pre styled)</h4>
                <pre><?php echo $spot['description']; ?></pre>
            </div>
            <div class="comments">
            	<h4>Comments</h4>
					<ul>
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
            </div>
		</div>

