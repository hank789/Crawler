<?php 
/**
 * Creating params
 * @param string $url
 * @return array
 */
function create_url_params_from_url($url)
{
	$parsed_url = parse_url($url);
	$params = array();
	$params['path']  = (isset($parsed_url['path'])) ? $parsed_url['path'] : '';
	$params['query']  = (isset($parsed_url['query'])) ? $parsed_url['query'] : null;
		
	return $params;
}

/**
 * Creating params
 * @param string $url
 * @return array
 */
function create_host_params_from_url($url)
{
	$parsed_url = parse_url($url);
	$params = array();
	$params['port']  = (isset($parsed_url['port'])&&$parsed_url['port']!=80) ? $parsed_url['port'] : 80;
	$params['https'] = (isset($parsed_url['scheme'])&&$parsed_url['scheme']=='https')?1:0;
	$params['host']  = $parsed_url['host'];
	return $params;
}