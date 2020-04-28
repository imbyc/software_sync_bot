<?php
require 'vendor/autoload.php';

try {

    $gen = new \App\Utils\Generation();

    // 生成全局数据
    $gen->genIndexList();
    $gen->genCategory();
    $gen->genGlobalSyncLog();
    $gen->genGlobalLastCheckTime();

} catch (\Exception $e) {
    showFailLog($e->getMessage());
}