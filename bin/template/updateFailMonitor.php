<?php
/**
* @file updateFailMonitor.php
* @author Tomorrow
* @description 批量更新失败的文档
*  
**/

require_once('./config.php');
$doc_id_file = fopen(LOG_PATH.'update.log.wf', 'r');

$temp_arr = array();

while( $line=fgets($doc_id_file) ){
	$doc_id = trim($line);

	$line = trim($line);
	$preg = "/info:\s(.+):false/";
	$matched_arr = array();

	$match = preg_match($preg, $line, $matched_arr);
	if( $match == 0 ) {
		continue;
	}

	$doc_id = $matched_arr[1];
	if( empty($doc_id) || in_array($doc_id, $temp_arr)){
		continue;
	}

	$temp_arr[] = $doc_id;

	echo $doc_id."\n";

    exec(PHP_PATH.' '.BIN_PATH.'update.php'.' '.$doc_id);
}

