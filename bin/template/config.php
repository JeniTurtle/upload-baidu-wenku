<?php
/**
* @file config.php
* @author Tomorrow
* @description 配置文件
*  
**/

// 根目录
$base_dir = 'E:/cygwin64/home/Tomorrow/wenku/';

// 复制template文件作为你的新项目
$project_name = 'template';

// 脚本执行文件夹目录
define('BIN_PATH', $base_dir.'bin/'.$project_name.'/');

// 需要上传的文件目录
define('DEFAULT_DATA_PATH', $base_dir.'data/'.$project_name.'/');

// 日志目录
define('LOG_PATH', $base_dir.'log/'.$project_name.'/');

// 核心文件库
define('LIB_PATH', $base_dir.'lib/');

// php版本
define('PHP_PATH', 'E:/wamp/bin/php/php5.5.12/php');

// 上传文件列表的索引文件夹
define('DOC_PATH_FILE', BIN_PATH."fileIndex/");

// 记录已上传信息的文件夹
define('UPLOAD_MONITOR_FILE', BIN_PATH."uploadMonitor/");

// cookie参数
define('BDUSS', 'nB1UWpIakZFUFU1MjFxSXpZV0ExNXhCSG5PN1QyS1N5T0VYVmNyRGdWanhkcnhXQVFBQUFBJCQAAAAAAAAAAAEAAAB9vLgwtbqzx8rpt9sAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPHplFbx6ZRWa');

// 设置updateMonitor.php执行时间，单位分钟
define('GAP', 0.2);

// 免费文档3，付费10
define('FLAG', 10);

// 是否私有，0为私有，1为公开
define('PRIVACY', 1);

// 是否可下载，0为可下载，1为不可下载，针对付费文档有效（FLAG=10)
define('DOWNLOADABLE', 0);

// 所需参数
define('UPLOAD_NEW', 1);

// FLAG为10时调用
$price_rule = array(
    // 文件价格
    'pay_price'=>25, 
    // 免费阅读页数
    'free_page'=>3,
);
