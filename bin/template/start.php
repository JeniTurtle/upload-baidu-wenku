<?php
/**
* @file start.php
* @author Tomorrow
* @description 开始文件
*  
**/

require_once('./config.php');

// 命令行参数
$process_num = !empty($argv[1]) ? intval($argv[1]) : '';

$counter_file = UPLOAD_MONITOR_FILE.'uploadMonitor'.$process_num.'.counter';
$uploaded_file = UPLOAD_MONITOR_FILE.'uploadMonitor'.$process_num.'.uploaded';
$error_file = UPLOAD_MONITOR_FILE.'uploadMonitor'.$process_num.'.error';

// 创建log文件夹
if (!file_exists(LOG_PATH)) { 
    mkdir(iconv("UTF-8", "GBK", LOG_PATH), 0777, true);
    echo 'Successfully create the log folder!';
}

// 创建fileIndex文件夹
if (!file_exists(DOC_PATH_FILE)) { 
    mkdir(iconv("UTF-8", "GBK", DOC_PATH_FILE), 0777, true);
    echo 'Successfully create the fileIndex folder!';
}

// 执行index/mkOriIndex.php文件，并将输出到index文件
$mkOriIndexPath = BIN_PATH.'index/mkOriIndex.php';
$command = PHP_PATH.' mkOriIndex.php > '.DOC_PATH_FILE.'index'.$process_num."\n";
`$command`;

// 执行uploadMonitor.php文件
$command = PHP_PATH.' '.BIN_PATH.'uploadMonitor.php'.' '.$process_num;
exec($command);
