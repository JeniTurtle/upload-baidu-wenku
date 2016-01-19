<?php
/**
* @file updateMonitor.php
* @author Tomorrow
* @description 批量更新已上传的文档数据
*  
**/

require_once("./config.php");

// 打开Log目录
$current_dir = opendir(LOG_PATH);

while(($file = readdir($current_dir)) !== false) {
	$sub_dir = LOG_PATH . $file;
	if($file == '.' || $file == '..') {
		continue;
	}else{
		$file_arr = explode('.', $file);

		if (strstr($file, 'upload') && array_pop($file_arr) == 'log') {
            updateByFile(fopen($sub_dir, 'r'));
            $new_sub_dir = str_replace('upload', 'update', $sub_dir);
            if (!file_exists($new_sub_dir)) {
                @rename($sub_dir, $new_sub_dir);
            } else {
                mergeFile($new_sub_dir, $sub_dir);
            }
			echo $new_sub_dir."\n";
		}
    }
}

function mergeFile($target, $source) {
    $content = file_get_contents($source);
    file_put_contents($target, $content, FILE_APPEND);
    @unlink($source);
}

function updateByFile($doc_id_file) {

    while( $line = fgets($doc_id_file) ){
        $line = trim($line);

		// 正则匹配doc_id
		$preg = "/doc_id:(.+)\s/";
		$matched_arr = array();

		$match = preg_match($preg, $line, $matched_arr);
		if( $match==0 ){
			continue;
		}
		$doc_id = $matched_arr[1];
		if( empty($doc_id) ){
			continue;
        }

		// 执行update.php
		exec(PHP_PATH.' '.BIN_PATH.'update.php'.' '.$doc_id);
	}
}

