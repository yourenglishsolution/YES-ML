<?php
	$nbItem = 3;
	$cache = './../rss_cache/rss.xml';
	$expire = time() - 7200; // valable 2 heure
	$xml = null;
	
	// Cache du fil RSS
	if(true || file_exists($cache) && filemtime($cache) > $expire)
	{
		$content = file_get_contents($cache);
		$xml = simplexml_load_string($content);
	}
	else
	{
		$rss = "http://feeds.bbci.co.uk/news/world/europe/rss.xml";
		$content = file_get_contents($rss);
		
		if($content !== false)
		{
			$xml = simplexml_load_string($content);
			file_put_contents($cache, $content);
		}
	}

?>
<div class="right">
	<div class="top">
		<span class="title">Actualités</span>
	</div>
	
	<div class="clear"></div>
	
	<div class="page">
		<h3>News BBC :</h3>
		<?
			foreach($xml->channel->item as $item)
			{
				if($nbItem <= 0) continue;
				else $nbItem--;
		?>
		<p><a href="<?=$item->link?>" target="_blank"><?=$item->title?></a></p>
		<div class="blockLine"></div>
		<? } ?>
	</div>
</div>