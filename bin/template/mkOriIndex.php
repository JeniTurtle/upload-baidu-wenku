<?php
/**
* @file mkOriIndex.php
* @author Tomorrow
* @description 遍历所有上传文件 
*  
**/

require_once("./config.php");

traverse(DEFAULT_DATA_PATH);

function traverse($path = '.') {
	$current_dir = opendir($path);
	while(($file = readdir($current_dir)) !== false) {
		$sub_dir = $path .'/'. $file;
		if($file == '.' || $file == '..') {
			continue;
		}elseif(is_dir($sub_dir)){
            traverse($sub_dir);
		}else{
            $file_arr = explode('.', $file);

            if( count($file_arr) > 1 ){
                array_pop($file_arr);
            }

            $title = implode('.', $file_arr);
			echo "$title\t$sub_dir\n";
		}
	}
}

