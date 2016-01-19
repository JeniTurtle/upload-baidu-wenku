utf-8

###文库批量上传脚本###

###使用步骤###
1, 决定一个上传代号，比如 tomorrow
2, cd bin; cp -r template tomorrow
3, cd tomorrow
4, vim config.php
    将 $base_dir 赋值为你正在阅读的这个read.me所在的绝对路径（一般不用动）
    将 $project_name 赋值为'tomorrow'
    将 PHP_PATH define为你想用的php，版本5以上吧（一般也不用动）
    将 BDUSS define为你要伪装的上传账号，登陆以后从cookie里取
    GAP 的含义是文档上传多久以后update，单位为分钟，可不设置，随后手动update
    FLAG含义为文档flag，取值同文库
    PRIVACY 含义为是否传成公开文档，1为公开，0为私有
    DOWNLOADABLE 含义为是否可下载，针对付费文档有效（flag=10）
    UPLOAD_NEW 不必理会
    $data_paths 为存放文档文件的路径，可设置多个，默认情况下，所有路径下的所有文件将会被上传
    $pay_price_rule & $price_rule 含义和用法参见注释
5, php start.php 上传文档并设置文档信息
6, php updateFailMonitor.php 重新设置更新失败的文档
   
