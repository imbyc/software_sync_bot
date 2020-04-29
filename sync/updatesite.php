<?php
/**
 * 基础数据更新
 * 当修改了软件的一些基本信息,如修改了软件的描述,更换了图片,等等,在代码Push到仓库的时候触发
 * 去更新这些基础数据,不是同步
 */
require 'vendor/autoload.php';

use App\Utils\Generation;

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
    try {
        // 将release置为空,因为这里不是同步,只是基础数据更新
        $c['release'] = [];
        $processedData = $c;

        $gen = new Generation();

        $gen->setSoftData($processedData);

        // 只有这两项需要更新
        $gen->genRelease();
        $gen->genLastCheckTime();

        // 全局数据更新这个
        $gen->genIndexList();
        $gen->genCategory();
        $gen->genGlobalLastCheckTime();

    } catch (\Exception $e) {
        showFailLog($e->getMessage());
    }
}
