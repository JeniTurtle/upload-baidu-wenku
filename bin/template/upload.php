<?php
/**
* @file upload.php
* @author Tomorrow
* @description 上传文库
*  
**/

require_once('./config.php');
require_once(LIB_PATH.'Upload.php');
require_once(LIB_PATH.'Http.php');
require_once(LIB_PATH.'Log.php');
require_once(LIB_PATH.'Log.class.php');

$cookie = 'BDUSS='.BDUSS;
$bduss = BDUSS;
$flag = FLAG;

$path = urldecode($argv[1]);
$title = urldecode($argv[2]);
$file_name_arr = explode(".", $path);
$ext = array_pop($file_name_arr);

if( !mb_check_encoding($title, 'UTF-8') ){
    $title = mb_convert_encoding($title,'UTF-8', 'GBK');
}

if( empty($title) || empty($ext) ){
    Plat_Log::fatal('empty title or ext in:'.$file);
    exit(0);
}

$min = date('i');
$date_tail = intval($min/GAP) * GAP;

$log_name = sprintf('%s%s%02d%s', 'upload', date("YmdH"), $date_tail, '.log');

Plat_Log::setOdp(false);
Plat_Log::setLogPath(LOG_PATH.$log_name);

$upload = new Plat_Upload($cookie);
for( $i=0;$i<2;$i++ ){
    $doc_id = $upload->run($title, $ext, $path, $flag);
    if(!empty($doc_id)){
        break;
    }
}

if ( empty($doc_id) ) {
    Plat_Log::debug("path:".$argv[1].":file:".$argv[2]);
    exit(0);
}else{
    echo $doc_id."\n";
    Plat_Log::addNotice('uploaded doc_id',$doc_id);
}

exit(0);
