<?php
if (PHP_SAPI != "cli") die('Direct access forbidden');
// It may take a whils to crawl a site ...
set_time_limit(0);
define('_VALID_ACCESS',1);
// Inculde the phpcrawl-mainclass
require_once 'include.php';


$hots=new Hosts();
$hosts_arr=$hots->all();
$log=new Log();
$lb = "\n";

if (!empty($hosts_arr)){
	foreach ($hosts_arr as $host){
		$crawler = new MyCrawler($host['id'],$host['link_post_date_rule']);
		
		// URL to crawl
		$crawler->setURL($host['host']);
		
		// Only receive content of files with content-type "text/html"
		$crawler->addContentTypeReceiveRule("#text/html#");
		
		// Ignore links to pictures, dont even request pictures
		$crawler->addURLFilterRule("#(jpg|gif|png|pdf|jpeg|css|js)$# i");
		$url_follow_rules=json_decode($host['url_follow_rule']);
		if (!empty($url_follow_rules)){
			foreach ($url_follow_rules as $url_follow_rule){
				$crawler->addURLFollowRule($url_follow_rule);
			}
		}
		
		//$crawler->goMultiProcessed(5);
		$crawler->setWorkingDirectory("/dev/shm/");
		$crawler->setUrlCacheType(PHPCrawlerUrlCacheTypes::URLCACHE_SQLITE);
		// Store and send cookie-data like a browser does
		$crawler->enableCookieHandling(true);
		//$crawler->setFollowMode(2);
		
		// Set the traffic-limit to 1 MB (in bytes,
		// for testing we dont want to "suck" the whole site)
		//$crawler->setTrafficLimit(20000 * 1024);
		
		// Thats enough, now here we go
		$crawler->go();
		
		// At the end, after the process is finished, we print a short
		// report (see method getProcessReport() for more information)
		$report = $crawler->getProcessReport();
		
		$message="";
		$message .= "Host:".$host['host'].$lb;
		$message .= "Links followed: ".$report->links_followed.$lb;
		$message .= "Links inserted: ".$crawler->count.$lb;
		$message .= "Documents received: ".$report->files_received.$lb;
		$message .= "Kb received: ".round($report->bytes_received / 1000).$lb;
		$message .= "Data throughput kb/s: ".round($report->data_throughput / 1000).$lb;
		$message .= "Process runtime: ".$report->process_runtime." sec".$lb;
		$message .= "Max memory-usage in KB: ".number_format($report->memory_peak_usage / 1024, 2, ".", "").$lb;
		$log->write($message);
	}
	$urls=new Urls();
	$urls_arr=$urls->find_by_post_date(date('Y-m-d H:i:s',strtotime('-3 days')));
	//rss feed file
	require_once 'vendor/FeedWriter/Item.php';
	require_once 'vendor/FeedWriter/Feed.php';
	require_once 'vendor/FeedWriter/RSS2.php';
	
	// Creating an instance of RSS2 class.
	$TestFeed = new \FeedWriter\RSS2;
	
	// Setting some basic channel elements. These three elements are mandatory.
	$TestFeed->setTitle('SRMBox.com News Feed');
	$TestFeed->setLink('http://srmbox.com');
	$TestFeed->setDescription('News Feed.');
	
	// Image title and link must match with the 'title' and 'link' channel elements for RSS 2.0,
	// which were set above.
	$TestFeed->setImage('News Feed', 'http://srmbox.com', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Rss-feed.svg/256px-Rss-feed.svg.png');
	
	// Use core setChannelElement() function for other optional channel elements.
	// See http://www.rssboard.org/rss-specification#optionalChannelElements
	// for other optional channel elements. Here the language code for American English and
	//$TestFeed->setChannelElement('language', 'en-US');
	
	// The date when this feed was lastly updated. The publication date is also set.
	$TestFeed->setDate(date(DATE_RSS, time()));
	//$TestFeed->setChannelElement('pubDate', date(\DATE_RSS, strtotime('2013-04-06')));
	
	// You can add additional link elements, e.g. to a PubSubHubbub server with custom relations.
	// It's recommended to provide a backlink to the feed URL.
	//$TestFeed->setSelfLink('http://example.com/myfeed');
	//$TestFeed->setAtomLink('http://pubsubhubbub.appspot.com', 'hub');
	
	// You can add more XML namespaces for more custom channel elements which are not defined
	// in the RSS 2 specification. Here the 'creativeCommons' element is used. There are much more
	// available. Have a look at this list: http://feedvalidator.org/docs/howto/declare_namespaces.html
	//$TestFeed->addNamespace('creativeCommons', 'http://backend.userland.com/creativeCommonsRssModule');
	//$TestFeed->setChannelElement('creativeCommons:license', 'http://www.creativecommons.org/licenses/by/1.0');
	
	// If you want you can also add a line to publicly announce that you used
	// this fine piece of software to generate the feed. ;-)
	$TestFeed->addGenerator();
	
	// Here we are done setting up the feed. What's next is adding some feed items.
	if (!empty($urls_arr)){
		foreach ($urls_arr as $url_link){
			// Create a new feed item.
			$newItem = $TestFeed->createNewItem();
			
			// Add basic elements to the feed item
			// These are again mandatory for a valid feed.
			$newItem->setTitle($url_link['linktext']);
			$newItem->setLink($url_link['path']);
			$newItem->setDescription($url_link['linktext']);
			
			// The following method calls add some optional elements to the feed item.
			
			// Let's set the publication date of this item. You could also use a UNIX timestamp or
			// an instance of PHP's DateTime class.
			$newItem->setDate($url_link['link_post_date']);
			
			// You can also attach a media object to a feed item. You just need the URL, the byte length
			// and the MIME type of the media. Here's a quirk: The RSS2 spec says "The url must be an http url.".
			// Other schemes like ftp, https, etc. produce an error in feed validators.
			//$newItem->setEnclosure('http://upload.wikimedia.org/wikipedia/commons/4/49/En-us-hello-1.ogg', 11779, 'audio/ogg');
			
			// If you want you can set the name (and email address) of the author of this feed item.
			//$newItem->setAuthor('Anis uddin Ahmad', 'admin@ajaxray.com');
			
			// You can set a globally unique identifier. This can be a URL or any other string.
			// If you set permaLink to true, the identifier must be an URL. The default of the
			// permaLink parameter is false.
			$newItem->setId($url_link['path'], true);
			
			// Use the addElement() method for other optional elements.
			// This here will add the 'source' element. The second parameter is the value of the element
			// and the third is an array containing the element attributes.
			//$newItem->addElement('source', 'Mike\'s page', array('url' => 'http://www.example.com'));
			
			// Now add the feed item to the main feed.
			$TestFeed->addItem($newItem);
		}
	}
	
	// OK. Everything is done. Now generate the feed.
	// If you want to send the feed directly to the browser, use the printFeed() method.
	$myFeed = $TestFeed->generateFeed();
	$date = new DateTime();
	$rss_file = 'rss/'. $date->format('Y-m-d').".xml";
	if(!file_exists($rss_file)) {
		$fh  = fopen($rss_file, 'a+') or die("Fatal Error !");
		fwrite($fh, $myFeed);
		fclose($fh);
	}
	else {
		file_put_contents($rss_file,$myFeed);
	}
}