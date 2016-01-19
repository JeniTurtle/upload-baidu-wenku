<?php
/**
* @file update.php
* @author Tomorrow
* @description 设置已上传的文件信息
*  
**/

require_once("./config.php");
require_once(LIB_PATH.'Upload.php');
require_once(LIB_PATH.'Http.php');
require_once(LIB_PATH.'Log.php');
require_once(LIB_PATH.'Log.class.php');
require_once(LIB_PATH.'func.php');

$cookie = 'BDUSS='.BDUSS;
$bduss = BDUSS;
$flag = FLAG;
$privacy = PRIVACY;
$downloadable = DOWNLOADABLE;
$new_upload = UPLOAD_NEW;

$doc_id = $argv[1];

if( empty($doc_id) ){
    Plat_Log::fatal('empty doc id:'.$doc_id);
    exit(0);
}

// 设置update的log文件路径
Plat_Log::setLogPath(LOG_PATH.'update.log');

Plat_Log::debug('update start doc_id:'.$doc_id);

$upload = new Plat_Upload($cookie);

// 获取已上传的文档信息
$doc_info_ret = $upload->getDocInfo($doc_id, 'json');

for( $i=0; $i<2; $i++ ){
    // 验证获取的文档信息
    if( docInfoOk($doc_info_ret, $doc_id) ){
        break;
    }
    sleep($i*1);
    // 如果没有信息，再次获取
    $doc_info_ret = $upload->getDocInfo($doc_id, 'json');
}

// 如果还是没有获取到，则输出到错误日志，并退出
if( !docInfoOk($doc_info_ret, $doc_id) ){
    Plat_Log::fatal('no doc info: '.$doc_id.':'.json_encode($doc_info_ret));
    exit(0);
}

// 打印文档信息到日志
Plat_Log::debug('doc_info:'.json_encode($doc_info_ret));

// 获取文档信息
$doc_info = $doc_info_ret['data'][$doc_id];

// 获取上传文档的页数
$file_page = intval($doc_info['page']);

// 获取文件后缀
$ext = $doc_info['type'];
$type = getTypeByExt($ext);
if( empty($type) ){
    Plat_Log::fatal('get type error');
    exit(0);
}

// 获取文件大小
$size = $doc_info['size'];

// 获取文件标题
$title = $doc_info['title'];

// 获取默认文档分类
$auto_class = $upload->getAutoClass($doc_id, $title, $size, $type);

Plat_Log::debug('auto class:'.json_encode($auto_class));

for( $i=0; $i<2; $i++ ){
    // 验证分类
    if( autoClassOk($auto_class) ){
        break;
    }
    sleep($i*1);
    $auto_class = $upload->getAutoClass($doc_id, $title, $size, $type);
}

if( !autoClassOk($auto_class) ){
    Plat_Log::fatal('get cid error:'.json_encode($auto_class));
    exit(0);
}

$cid1 = $auto_class['cid1'];
$cid2 = $auto_class['cid2'];
$cid3 = $auto_class['cid3'];
$cid4 = 0;

$tag_str = '';
$summary = '';

// 截取文件名
if( strlen($title) > 100 ){
    $title = mb_substr($title, 0, 90);
}

// 判断是否是付费文档
if( $flag == 10 ){
    $update_ret = $upload->update($title, $summary, $cid1, $cid2, $cid3, $cid4, $tag_str, $privacy, $flag, $price_rule['pay_price'], $price_rule['free_page'], $downloadable, $doc_id, $new_upload);
}else{
    $update_ret = $upload->updateFree($title, $summary, $cid1, $cid2, $cid3, $cid4, $tag_str, $privacy, $flag, $downloadable, $doc_id, $new_upload);
}
var_dump($update_ret);

Plat_Log::addNotice('update ret', json_encode($update_ret));

if( !$update_ret || $update_ret['errno']!=0 ){
    Plat_Log::fatal('update error:'.json_encode($update_ret).':'.$doc_id);
    exit(0);
}

exit(0);

function docInfoOk($doc_info_ret, $doc_id){
    if( empty($doc_id) ){
        return false;
    }
    if( empty($doc_info_ret['data'][$doc_id]['page']) ){
        return false;
    }
    if( empty($doc_info_ret['data'][$doc_id]['title']) ){
        return false;
    }
    if( empty($doc_info_ret['data'][$doc_id]['size']) ){
        return false;
    }
    if( empty($doc_info_ret['data'][$doc_id]['type']) ){
        return false;
    }
    return true;
}

function autoClassOk($auto_class){
    if( !isset($auto_class['cid1']) ){
        return false;
    }
    if( !isset($auto_class['cid2']) ){
        return false;
    }
    if( !isset($auto_class['cid3']) ){
        return false;
    }
    return true;
}
