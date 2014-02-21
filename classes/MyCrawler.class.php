<?php
class MyCrawler extends PHPCrawler
{
	var $url_object='';
	var $host_id='';
	var $link_post_date_rule='';
	var $day=2;
	var $count=0;
	//var $logs='';
	function __construct ($host_id,$link_post_date_rule,$day=2)
	{
		parent::__construct();
		$this->host_id=$host_id;
		$this->link_post_date_rule=$link_post_date_rule;
		$this->day=$day;
		$this->url_object=new Urls();
		//$this->logs=new Log();
	}
	function handlePageData(&$page_data) {
		$exist=$this->url_object->find_by_path($page_data['url']);
		if (empty($exist) && $page_data['http_status_code']=='200'){
			
			if (stristr($page_data['refering_linkcode'], 'title="')){
				$html = file_get_html($page_data['url']);
				//$this->logs->write($page_data['url']);
				$ret = $html->find($this->link_post_date_rule,0);
				$tt=preg_replace('/[^\d-\s\:]+/', '', $ret->plaintext);
				$tt=trim($tt);
				$tt=trim($tt,':');
				$tt=trim($tt);
				if (date('Y/m/d',strtotime($tt))>=date('Y/m/d',strtotime('-'.$this->day.' days'))){
					$this->url_object->link_post_date=date('Y-m-d H:i:s',strtotime($tt));
					$this->url_object->host_id=$this->host_id;
					$this->url_object->path=$page_data['url'];
					$encode = mb_detect_encoding($page_data['refering_linktext'], array('ASCII','UTF-8','GB2312','GBK','BIG5'));
					$this->url_object->linktext=iconv($encode,'UTF-8',$page_data['refering_linktext']);
						
					$this->url_object->create();
					$this->count++;
				}
			}
		}
		/*$lb="<br>";
		echo 'url:'.$page_data['url'].$lb;
		echo 'referer_url:'.$page_data['referer_url'].$lb;
		echo 'refering_linkcode:'.$page_data['refering_linkcode'].$lb;
		echo 'refering_link_raw:'.$page_data['refering_link_raw'].$lb;
		echo 'refering_linktext:'.$page_data['refering_linktext'].$lb;
		echo $lb;
		flush();*/
	}
}