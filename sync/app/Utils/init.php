<?php

// 初始化系统

define('ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))));

// 系统配置
$GLOBALS['systemConfig'] = new \App\Utils\Config('config');

// 本地测试用,设置环境变量
putenv("QINIU_ACCESS_KEY=RrTXes7uP43FcLPSTmqMGC2KhGt0XDGCexBFs4PB");
putenv("QINIU_SECRET_KEY=tJVpB_DetaPdvcJq-oke6nzzgwv7dDlwn20QE64p");

// 本地测试用,代理设置, IP为物理机IPv4地址
//define('PROXY', '192.168.1.6:10809');
define('PROXY', '192.168.1.3:10809');
define('PROXY_TYPE', CURLPROXY_HTTP);