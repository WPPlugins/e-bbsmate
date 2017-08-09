<?php
if(!defined('ABSPATH')) exit;

// HTMLPurifier 클래스 include
if(!class_exists('HTMLPurifier')){
	include_once PavoBoardMate::$PLUGIN_DIR.'include/htmlpurifier/HTMLPurifier.standalone.php';
}

wp_mkdir_p(WP_CONTENT_DIR.'/uploads/ebbsmate_htmlpurifier');

/**
 * Cross-site scripting (XSS) 공격을 문자열을 제거한다.
 * @param string $content
 */
function ebbsmate_html_purifier($content){
	if(is_array($data)) return array_map('ebbsmate_xssfilter', $data);
	
	$HTMLPurifier_Config = HTMLPurifier_Config::createDefault();
	$HTMLPurifier_Config->set('URI.AllowedSchemes', array('http'=>true,'https'=>true,'mailto'=>true));
	$HTMLPurifier_Config->set('URI.SafeIframeRegexp', '(.*)');
	$HTMLPurifier_Config->set('HTML.SafeIframe', true);
	$HTMLPurifier_Config->set('HTML.SafeObject', true);
	$HTMLPurifier_Config->set('HTML.SafeEmbed', true);
	$HTMLPurifier_Config->set('HTML.TidyLevel', 'light');
	$HTMLPurifier_Config->set('HTML.FlashAllowFullScreen', true);
	$HTMLPurifier_Config->set('HTML.AllowedElements','img,div,a,strong,font,span,em,br,p,u,i,b,sup,sub,small,table,thead,tbody,tfoot,tr,td,th,caption,pre,code,ul,li,ol,big,code,blockquote,center,hr,h1,h2,h3,h4,h5,h6,iframe');
	$HTMLPurifier_Config->set('HTML.AllowedAttributes', 'a.href,a.target,img.src,iframe.src,iframe.frameborder,*.id,*.alt,*.style,*.class,*.title,*.width,*.height,*.border,*.colspan,*.rowspan');
	$HTMLPurifier_Config->set('Attr.AllowedFrameTargets', array('_blank'));
	$HTMLPurifier_Config->set('Output.FlashCompat', true);
	$HTMLPurifier_Config->set('Core.RemoveInvalidImg', true);
	$HTMLPurifier_Config->set('Cache.SerializerPath', WP_CONTENT_DIR.'/uploads/ebbsmate_htmlpurifier');
	$GLOBALS['EBBSMATE']['HTMLPurifier_Config'] = $HTMLPurifier_Config;
	$GLOBALS['EBBSMATE']['HTMLPurifier'] = HTMLPurifier::getInstance();
	unset($HTMLPurifier_Config);
	
	return ebbsmate_safeiframe($content);
}

/**
 * 허용된 도메인의 아이프레임만 남기고 모두 제거.
 * @param string $data
 * @return string
 */
function ebbsmate_safeiframe($data){
	/*
	 * 허가된 도메인 호스트 (화이트 리스트)
	 */
	$whilelist[] = 'youtube.com';
	$whilelist[] = 'www.youtube.com';
	$whilelist[] = 'maps.google.com';
	$whilelist[] = 'maps.google.co.kr';
	$whilelist[] = 'serviceapi.nmv.naver.com';
	$whilelist[] = 'serviceapi.rmcnmv.naver.com';
	$whilelist[] = 'videofarm.daum.net';
	$whilelist[] = 'player.vimeo.com';
	$whilelist[] = 'w.soundcloud.com';
	$whilelist[] = 'slideshare.net';
	$whilelist[] = 'www.slideshare.net';
	$whitelist[] = 'mgoon.com';
	$whitelist[] = 'www.mgoon.com';

	$re = preg_match_all('/<iframe.+?src="(.+?)".+?[^>]*+>/is', $data, $matches);
	$iframe = $matches[0];
	$domain = $matches[1];

	foreach($domain AS $key => $value){
		$value = 'http://' . preg_replace('/^(http:\/\/|https:\/\/|\/\/)/i', '', $value);
		$url = parse_url($value);
		if(!in_array($url['host'], $whilelist)){
			$data = str_replace($iframe[$key].'</iframe>', '', $data);
			$data = str_replace($iframe[$key], '', $data);
		}
	}
	
	$data = preg_replace('/<iframe(.*?)>/is', '<iframe$1 allowfullscreen>', $data);
	
	return $data;
}


/**
 * 모든 html을 제거한다.
 * @param object $data
 */
function ebbsmate_htmlclear($data){
	if(is_array($data)) return array_map('ebbsmate_htmlclear', $data);
	$data = strip_tags($data);
	return htmlspecialchars($data);
}
