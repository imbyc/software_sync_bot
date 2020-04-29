<?php
/**
 * 同步任务
 */
require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

define('SOFTNAME', '*');

// 遍历config目录
$syncConfig = [];
foreach (glob(ROOT_PATH . '/config/soft/' . SOFTNAME . '.yml') as $filename) {
    try {
        array_push($syncConfig, Yaml::parseFile($filename));
    } catch (\Symfony\Component\Yaml\Exception\ParseException $exception) {
        printf('Unable to parse the YAML string: %s', $exception->getMessage());
        exit;
    }
}

if (empty($syncConfig)) {
    showFailLog("配置为空");
    exit;
}

foreach ($syncConfig as $c) {

    $processedData = \App\Utils\Process::run($c);

    file_put_contents(ROOT_PATH . '/' . $c['softname'] . '.txt', print_r($processedData, true));

    if (!$processedData) {
        continue;
    }

    // 对已解析的数据进行上传并处理,返回已成功上传的版本
    $processedData = \App\Utils\Sync::run($processedData);

    file_put_contents(ROOT_PATH . '/' . $c['softname'] . '-processed.txt', print_r($processedData, true));
    file_put_contents(ROOT_PATH . '/' . $c['softname'] . '-processed-json.txt', json_encode($processedData));

    // 生成数据
    try {

        $gen = new \App\Utils\Generation();

        $gen->setSoftData($processedData);

        $gen->genRelease();
        $gen->genLatestVersion();
        $gen->genLastCheckTime();
        $gen->genReleaseNotes();
        $gen->genSyncLog();
        $gen->genVersionData();

        // 生成全局数据
        $gen->genIndexList();
        $gen->genCategory();
        $gen->genGlobalSyncLog();
        $gen->genGlobalLastCheckTime();

    } catch (\Exception $e) {
        showFailLog($e->getMessage());
    }
}





