<?php
// 显示所有错误
ini_set('display_errors', true);
error_reporting(E_ALL);

// 初始化系统

define('ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))));

// 系统配置
$GLOBALS['systemConfig'] = new \App\Utils\Config('config');

// 加载.env文件
if (file_exists(ROOT_PATH . DIRECTORY_SEPARATOR . 'config/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH . DIRECTORY_SEPARATOR . 'config');
    $dotenv->load();
}

if (!getenv('QINIU_ACCESS_KEY')) {
    showFailLog('QINIU_ACCESS_KEY 未设置');
    exit(-1);
}
if (!getenv('QINIU_SECRET_KEY')) {
    showFailLog('QINIU_SECRET_KEY 未设置');
    exit(-1);
}
if (getenv('PROXY') && getenv('PROXY_TYPE') != null) {
    define('PROXY', getenv('PROXY'));
    define('PROXY_TYPE', getenv('PROXY_TYPE'));
    showNoticeLog('代理已设置', 'PROXY', getenv('PROXY'), 'PROXY_TYPE', getenv('PROXY_TYPE'));
    // 检查代理可用性
    checkProxy(PROXY, PROXY_TYPE);
}
