<?php
/**
* @file uploadMonitor.php
* @author Tomorrow
* @description 批量上传文件
*  
**/

require_once('./config.php');

$start_time = time();

// 命令行参数
$process_num = !empty($argv[1]) ? intval($argv[1]) : '';

$index_file = DOC_PATH_FILE."index".$process_num; 

$counter_file = UPLOAD_MONITOR_FILE.'uploadMonitor'.$process_num.'.counter';
$uploaded_file = UPLOAD_MONITOR_FILE.'uploadMonitor'.$process_num.'.uploaded';
$error_file = UPLOAD_MONITOR_FILE.'uploadMonitor'.$process_num.'.error';

// 创建uploadMonitor文件夹
if (!file_exists(UPLOAD_MONITOR_FILE)) { 
    mkdir(UPLOAD_MONITOR_FILE, 0777);
    echo 'Successfully create the uploadMonitor folder!';
}

// 创建uploadMonitor.counter文件
if (!file_exists($counter_file)) { 
    $temp_counter = fopen($counter_file, "w") or die("Unable to open file!");
    fclose($temp_counter);
}

// 创建uploadMonitor.uploaded文件
if (!file_exists($uploaded_file)) { 
    $temp_uploaded = fopen($uploaded_file, "w") or die("Unable to open file!");
    fclose($temp_uploaded);
}

// 获取当前已上传的文件数量
$mark = file_get_contents($counter_file);
$mark = intval(trim($mark));

if( $mark < 0 ){
	$mark = 0;
}

// 打开上传文件索引列表
$doc_path_file = fopen($index_file, 'r');

// 当前文件索引
$counter = 0;

while( $item = fgets($doc_path_file) ){

    if ( (time() - $start_time) > (GAP * 60) ) {
        exec(PHP_PATH.' '.BIN_PATH.'updateMonitor.php');
        $start_time = time();
    }

	// 获取每行内容
	$item = trim($item);

	// $item_arr[0]文件名 $item_arr[1]文件路径
	$item_arr = explode("\t", $item);

    if( empty($item_arr[0]) || empty($item_arr[1] ) ){

        // 创建uploadMonitor.err文件
        if (!file_exists($error_file)) { 
            $temp_error = fopen($error_file, "w") or die("Unable to open file!");
            fclose($temp_error);
        }

		// 写入uploadMonitor.err文件
        file_put_contents($error_file, 'empty title or path:'.$item."\n");
		continue;
    }

	$counter++;

	// 跳过已上传的文件
    if( $counter > $mark ){

		$title = trim($item_arr[0]);

		$path = trim($item_arr[1]);

		// 执行upload.php
        $command = PHP_PATH.' '.BIN_PATH.'upload.php'.' '.urlencode($path).' '.urlencode($title);

        $doc_id = exec($command);

		file_put_contents($counter_file, $counter."\n");
        file_put_contents($uploaded_file, $doc_id."\t".$item."\n", FILE_APPEND);
	}
}

exec(PHP_PATH.' '.BIN_PATH.'updateMonitor.php');

