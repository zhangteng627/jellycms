<?php
/**
 * @copyright (c) 2015-2099 www.guodong.tech
 * @file Common.php
 * @brief 自定义函数
 * @author zhangteng
 * @date 2014/7/14 23:11:50
 * @version 1.0.0
 */
 //前台 url
 function url($parrams){
	$config = new \Config\Config();
	if($config->rewriteRule == 'url'){
		count($parrams)>1?$url = '/?'.implode('/', $parrams).'.'.$config->suffix:$url = '/?'.implode('/', $parrams).'/';
		return $url;
	}else{
		count($parrams)>1?$url = '/'.implode('/', $parrams).'/'.$config->suffix:$url = '/'.implode('/', $parrams).'/';
		return $url;
	}
 }
 

	function post(){
		$args = func_get_args();
		$request = \Config\Services::request();
		if(count($args)){
			$args_str = implode(',', $args);
			return $request->getPost($args_str);
		}else{
			return $request->getPost();
		}
	}
	function get(){
		$args = func_get_args();
		$request = \Config\Services::request();
		if(count($args)){
			$args_str = implode(',', $args);
			return $request->getGet($args_str);
		}else{
			return 