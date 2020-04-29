<?php

namespace App\Utils;

class Process
{

    /**
     * 数据处理
     * @param $syncConfig 具体一个软件的同步配置
     * @param null $processPlatform 要处理的平台,可以单独处理某一个平台,默认处理全部
     * @param bool $forceSync 是否强制同步,默认为 false,不强制, 置为 true 时,会忽略 $syncConfig['softsync']==false 进行强制同步,可用于测试
     * @return mixed
     */
    public static function run($syncConfig, $processPlatform = null, $forceSync = false)
    {
        if (!$syncConfig['softname']) {
            showFailLog('softname为空');
            return false;
        }

        showLog('当前处理软件:', $syncConfig['softname']);

        if (!$forceSync && isset($syncConfig['softsync']) && !$syncConfig['softsync']) {
            showNoticeLog('同步设置 softsync 为 FALSE 无需同步');
            return false;
        }

        foreach ($syncConfig['release'] as $platform => $item) {
            if (!empty($processPlatform)) {
                if ((is_array($processPlatform) && !in_array($platform, $processPlatform)) || (!is_array($processPlatform) && $processPlatform != $platform)) {
                    unset($syncConfig['release'][$platform]);
                    continue;
                }
            }

            showLog('当前处理平台:', $platform);

            // 远程链接
            $link = $item['link'];
            // 远程链接返回内容类型 json, html
            $linktype = $item['linktype'];
            // 远程链接请求方法. GET , POST 默认GET
            $linkRequestMethod = $item['linkrequestmethod'] ?? 'GET';

            $parserRule = $item['rule'];

            // 获取解析器
            if (isset($parserRule['parser'])) {
                // 指定解析器
                $parserName = $parserRule['parser'];
            } else {
                // 默认解析器
                $parserName = '\App\Parser\Parser';
            }

            // 实例化解析器
            $parser = new $parserName();

            // 获取远程数据
            $remoteData = call_user_func_array([$parser, 'getRemoteData'], [$link, $linktype, $linkRequestMethod]);

            if (empty($remoteData)) {
                showFailLog('获取远程数据异常');
                unset($syncConfig['release'][$platform]);
                continue;
            }

            // 数据解析
            $remoteLists = call_user_func_array([$parser, 'process'], [$remoteData, $item]);

            // 设置默认值

            // 重组数组格式
            unset($item['link'], $item['linktype'], $item['linkrequestmethod'], $item['rule']);
            $syncConfig['release'][$platform] = $item;
            $syncConfig['release'][$platform]['lists'] = $remoteLists;

        }

        // 变量替换
        return Util::varsReplace($syncConfig);
    }
}