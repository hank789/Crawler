<?php
$date = new DateTime();
$rss_file = 'rss/'. $date->format('Y-m-d').".xml";
$contentType = "text/xml";
header("Content-Type: " . $contentType);
if (file_exists($rss_file))
	echo file_get_contents($rss_file);
else echo 'none';
