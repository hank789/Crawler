<?php
/*require_once 'simple_html_dom.php';
$html = file_get_html('http://zfcg.czfb.gov.cn/html/ns/zfjzcg_gzgg/16003125940640.html');
$ret = $html->find('td[style=border-top:1px solid #CCC]',0);

$tt=preg_replace('/[^\d-\s\:]+/', '', $ret->plaintext);
$tt=trim($tt);
$tt=trim($tt,':');
echo $tt;
//echo $ret->plaintext;