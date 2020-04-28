<?php
/**
 *  清理中转bucket的文件
 *  2020-4-20
 */

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

define('SOFTNAME', 'postman');

// 遍历config目录
$syncConfig = [];
foreach (glob(ROOT_PATH.'/config/soft/'.SOFTNAME.'.yml') as $filename) {
    try {
        array_push($syncConfig, Yaml::parseFile($filename));
    } catch (\Symfony\Component\Yaml\Exception\ParseException $exception) {
        printf('Unable to parse the YAML string: %s', $exception->getMessage());
        exit;
    }
}

if (empty($syncConfig)) exit('配置为空');

foreach ($syncConfig as $c) {

    $processedData = \App\Utils\Process::run($c);

    file_put_contents(ROOT_PATH.'/'.SOFTNAME.'.txt', json_encode($processedData));

    if (!$processedData) {
        continue;
    }

    // 对已解析的数据进行上传并处理,返回已成功上传的版本
    $processedData = \App\Utils\Sync::run($processedData);

    file_put_contents(ROOT_PATH.'/'.SOFTNAME.'-processed.txt', print_r($processedData, true));

    // 生成软件相关数据
    try {

        $gen = new \App\Utils\Generation();

        $gen->setSoftData($processedData);

        $gen->genRelease();
        $gen->genLatestVersion();
        $gen->genLatestCheckTime();
        $gen->genReleaseNotes();

    } catch (\Exception $e) {
        showLog($e->getMessage());
    }
}





