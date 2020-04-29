<?php

namespace App\Utils;

use Symfony\Component\Yaml\Yaml;

class Generation
{
    // 此次同步的数据
    private $syncData;
    // 老数据和此次同步数据合并后的数据
    private $mergedData;
    // 软件名称
    private $softName;
    // data/软件名称 目录
    private $softDataDir;
    // data目录
    private $dataDir = ROOT_PATH . '/data';
    // 项目根目录
    private $rootDir = ROOT_PATH;

    private $timeStamp;

    public function __construct()
    {
        $this->timeStamp = time();
    }


    public function setSoftData($data)
    {
        $this->softName = $data['softname'];
        $this->softDataDir = $this->dataDir . DIRECTORY_SEPARATOR . $this->softName;
        $this->syncData = $data;
        $this->mergedData = $this->softDataMerge($data);
    }

    /**
     * 生成软件最后检查时间
     * data/软件名称/lastchecktime.json
     */
    public function genLastCheckTime()
    {
        $genFilename = $this->softDataDir . '/lastchecktime.json';

        return Util::writeJsonFile($genFilename, ['timestamp' => $this->timeStamp]);
    }

    /**
     * data/软件名称/latestversion.json
     */
    public function genLatestVersion()
    {
        $genFilename = $this->softDataDir . '/latestversion.json';

        if ($this->mergedData['release']) {
            $data = [];
            foreach ($this->mergedData['release'] as $platform => $platItem) {
                if ($platItem['lists']) {
                    $latestVersionItem = array_shift($platItem['lists']);
                    if (!isset($data['datetime']) || !$data['datetime']) {
                        $data['datetime'] = $latestVersionItem['datetime'];
                    }
                    if (!isset($data['version']) || !$data['version']) {
                        $data['version'] = $latestVersionItem['version'];
                    }
                    if (!isset($data['timestamp']) || !$data['timestamp']) {
                        $data['timestamp'] = $latestVersionItem['timestamp'];
                    }
                    $data['latestLists'][$platform] = $latestVersionItem;
                    $data['latestLists'][$platform]['platform'] = $this->mergedData['release'][$platform]['platform'];
                    $data['latestLists'][$platform]['platformshowname'] = $this->mergedData['release'][$platform]['platformshowname'];
                }
            }
        }

        return Util::writeJsonFile($genFilename, $data);
    }

    /**
     * data/软件名称/release.json
     */
    public function genRelease()
    {
        $genFilename = $this->softDataDir . '/release.json';

        return Util::writeJsonFile($genFilename, $this->mergedData);
    }

    /**
     * app/软件名称/平台/版本/releasenotes.html
     */
    public function genReleaseNotes()
    {
        if ($this->mergedData['release']) {
            foreach ($this->mergedData['release'] as $platform => $item) {
                if ($item['lists']) {
                    foreach ($item['lists'] as $version => $versionItem) {
                        $htmlTitle = sprintf('%s(%s) %s Release Notes [%s]', $this->softName, $platform, $version, date('Y-m-d', $versionItem['timestamp']));
                        $htmlHeader = <<<EOF
<!DOCTYPE html><html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>${htmlTitle}</title><style>body{margin:0;font-family:Roboto,Arial,sans-serif;background-color:#fff;line-height:1.3;text-align:center;color:#3e4042}#page{width:100%}#page.wide>.container,.Footer--wide,.Header-nav--wide{max-width:none}#page>.container,.Header-nav{text-align:left;margin-left:auto;margin-right:auto;padding:0 1.25rem}h1{font-size:1.75rem;line-height:1}h1,h2,h3,h4{margin:1.25rem 0;padding:0;color:#202224;font-family:Work Sans,sans-serif;font-weight:600}h2{clear:right;font-size:1.25rem;background:#e0ebf5;padding:.5rem;line-height:1.25;font-weight:400;overflow-wrap:break-word}p,ul{margin:1.25rem}li,p{max-width:50rem;word-wrap:break-word}</style></head><body><main id="page" class="wide"><div class="container">
EOF;
                        $htmlFooter = <<<EOF
</div></main></body></html>
EOF;
                        $htmlBody = "<h1>${htmlTitle}</h1>";
                        if ($versionItem['notes']) {
                            $htmlBody .= "<h2>更新日志</h2>";
                            if (is_array($versionItem['notes'])) {
                                foreach ($versionItem['notes'] as $key => $item) {
                                    if (!is_int($key)) {
                                        $htmlBody .= "<p>$key</p>";
                                    }
                                    if (is_array($item)) {
                                        if (Util::isIndexedArray($item)) {
                                            $htmlBody .= '<ul>';
                                            foreach ($item as $value) {
                                                $htmlBody .= "<li>$value</li>";
                                            }
                                            $htmlBody .= '</ul>';
                                        }
                                    } else {
                                        $htmlBody .= "<p>$item</p>";
                                    }
                                }
                            }
                        }

                        if ($versionItem['features']) {
                            $htmlBody .= "<h2>新特性</h2>";
                            if (is_array($versionItem['features'])) {
                                foreach ($versionItem['features'] as $key => $item) {
                                    if (!is_int($key)) {
                                        $htmlBody .= "<p>$key</p>";
                                    }
                                    if (is_array($item)) {
                                        if (Util::isIndexedArray($item)) {
                                            $htmlBody .= '<ul>';
                                            foreach ($item as $value) {
                                                $htmlBody .= "<li>$value</li>";
                                            }
                                            $htmlBody .= '</ul>';
                                        }
                                    } else {
                                        $htmlBody .= "<p>$item</p>";
                                    }
                                }
                            }
                        }

                        $ret = Util::writeFile($versionItem['gennotespagepath'], $htmlHeader . $htmlBody . $htmlFooter);

                    }
                }
            }
        }
    }


    /**
     * 为每个平台每个版本单独生成数据文件
     * data/软件名称/平台/版本/version.json
     */
    public function genVersionData()
    {
        if ($this->syncData && $this->syncData['release']) {
            foreach ($this->syncData['release'] as $platform => $platItem) {
                if (!isset($platItem['lists']) || empty($platItem['lists'])) continue;
                foreach ($platItem['lists'] as $version => $versionItem) {
                    if (!isset($versionItem['downloadList']) || empty($versionItem['downloadList'])) continue;
                    Util::writeJsonFile(
                        $this->softDataDir . DIRECTORY_SEPARATOR .
                        $platform . DIRECTORY_SEPARATOR .
                        $version . DIRECTORY_SEPARATOR .
                        'version.json',
                        $versionItem
                    );
                }
            }
        }
    }

    // {
    // 	softlogo: '软件logo',
    // 	softshowname: 'iText 显示名称',
    //  softname: '软件名称',
    //  softshortcomment: '右上角一句话说明',
    //  softspecialtip: '特别说明',
    // 	softicon: ['paid'],  // 含内购或付费:paid  免费:free
    // 	softcomment: '软件说明软件描述',
    // 	softbanner: 'banner图片',
    // 	latestupdatedate: '2019-07-08',
    // 	latestversion:'(V 1.6.2) 最新版本',
    // 	softcategory:['tool']  分类
    // 	softdownloadlink:'软件下载地址',
    // }

    /**
     * 生成首页列表数据
     * data/list.json
     */
    public function genIndexList()
    {
        $list = [];
        foreach (glob($this->rootDir . '/config/soft/*.yml') as $filename) {
            $cfg = (new Config($filename))->get();
            // 是否显示在首页,默认为true
            if (isset($cfg['softshowinlist']) && $cfg['softshowinlist'] == false) {
                continue;
            }
            // 移除release键
            unset($cfg['release']);
            $list[$cfg['softname']] = $cfg;
        }

        // 新数据和老数据合并
        $list = $this->softListMerge($list);

        $genFilename = $this->dataDir . '/list.json';
        return Util::writeJsonFile($genFilename, $list);
    }

    /**
     * 生成分类数据
     * data/category.json
     * @return false|int
     */
    public function genCategory()
    {
        $genFilename = $this->dataDir . '/category.json';
        return Util::writeJsonFile($genFilename, (new Config('category'))->get());
    }

    /**
     * 生成某个软件的同步日志
     *  data/软件名称/synclog.json
     */
    public function genSyncLog()
    {
        $genFilename = $this->softDataDir . '/synclog.json';
        // 老数据
        $oldData = [];
        if (file_exists($genFilename)) {
            $oldData = json_decode(file_get_contents($genFilename), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $oldData = [];
            }
        }

        if ($this->syncData && $this->syncData['release']) {
            foreach ($this->syncData['release'] as $platform => $platItem) {
                if (!isset($platItem['lists']) || empty($platItem['lists'])) continue;
                foreach ($platItem['lists'] as $version => $versionItem) {
                    if (!isset($versionItem['downloadList']) || empty($versionItem['downloadList'])) continue;
                    foreach ($versionItem['downloadList'] as $dwnFile) {
                        $date = date('Y-m-d', $this->timeStamp);
                        if (
                            isset($dwnFile['filethissync']) &&
                            $dwnFile['filethissync'] == true &&
                            (!isset($oldData[$date]) || array_search($dwnFile['filename'], array_column($oldData[$date], 'filename')) === false)
                        ) {
                            $oldData[$date][] = [
                                'platform' => $platform,
                                'platformshowname' => $platItem['platformshowname'],
                                'version' => $version,
                                'filename' => $dwnFile['filename']
                            ];
                        }
                    }
                }
            }
        }

        // 按日期排序
        Util::dateSort($oldData);

        return Util::writeJsonFile($genFilename, $oldData);
    }

    /**
     * 生成全局最后检查时间
     * data/lastchecktime.json
     */
    public function genGlobalLastCheckTime()
    {
        $genFilename = $this->dataDir . '/lastchecktime.json';

        return Util::writeJsonFile($genFilename, ['timestamp' => $this->timeStamp]);
    }

    /**
     * 生成所有软件同步日志
     *  data/synclog.json
     */
    public function genGlobalSyncLog()
    {
        $genFilename = $this->dataDir . '/synclog.json';
        // 老数据
        $oldData = [];
        if (file_exists($genFilename)) {
            $oldData = json_decode(file_get_contents($genFilename), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $oldData = [];
            }
        }

        if ($this->syncData && $this->syncData['release']) {
            foreach ($this->syncData['release'] as $platform => $platItem) {
                if (!isset($platItem['lists']) || empty($platItem['lists'])) continue;
                foreach ($platItem['lists'] as $version => $versionItem) {
                    if (!isset($versionItem['downloadList']) || empty($versionItem['downloadList'])) continue;
                    foreach ($versionItem['downloadList'] as $dwnFile) {
                        $date = date('Y-m-d', $this->timeStamp);
                        if (
                            isset($dwnFile['filethissync']) &&
                            $dwnFile['filethissync'] == true &&
                            (!isset($oldData[$date][$this->softName]) || array_search($dwnFile['filename'], array_column($oldData[$date][$this->softName], 'filename')) === false)
                        ) {
                            $oldData[$date][$this->softName][] = [
                                'platform' => $platform,
                                'platformshowname' => $platItem['platformshowname'],
                                'version' => $version,
                                'filename' => $dwnFile['filename']
                            ];
                        }
                    }
                }
            }
        }

        // 按日期排序
        Util::dateSort($oldData);

        $ret = Util::writeJsonFile($genFilename, $oldData);

        // 生成markdown文件
        $ret = $this->genGlobalSyncLogMd();

        return $ret;
    }

    /**
     * 根据data/synclog.json生成markdown格式文件
     * 此文件存放在代码库中
     * SYNCLOG.md
     */
    private function genGlobalSyncLogMd()
    {
        $genFilename = $this->rootDir . '/doc/SYNCLOG.md';
        $log = [];
        if (file_exists($this->dataDir . '/synclog.json')) {
            $log = json_decode(file_get_contents($this->dataDir . '/synclog.json'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $log = [];
            }
        }

        $mdText = '# 同步日志' . str_repeat(PHP_EOL, 2);

        if ($log) {
            foreach ($log as $date => $dateItem) {
                $mdText .= '## ' . $date . str_repeat(PHP_EOL, 2);
                foreach ($dateItem as $softName => $softItem) {
                    $mdText .= '### ' . $softName . str_repeat(PHP_EOL, 2);
                    foreach ($softItem as $num => $updateItem) {
                        $mdText .= ($num + 1) . ". `{$updateItem['platformshowname']}` `{$updateItem['version']}` {$updateItem['filename']}" . str_repeat(PHP_EOL, 1);
                    }
                }
            }
        }

        return Util::writeFile($genFilename, $mdText);
    }

    /**
     * 某个软件处理后的数据和老数据合并
     * @param $newData
     * @param null $softName
     * @return array|bool
     */
    private function softDataMerge($newData, $softName = null)
    {
        if (!$softName) {
            $softName = $newData['softname'] ?? null;
        }
        if (!$softName) {
            return false;
        }

        // 老数据
        $oldData = [];
        if (file_exists($this->softDataDir . '/release.json')) {
            $oldData = json_decode(file_get_contents($this->softDataDir . '/release.json'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $oldData = [];
            }
        }
        if (empty($oldData)) {
            return $newData;
        }

        if ($newData['release']) {
            foreach ($newData['release'] as $platform => &$newItem) {
                // 清洗数据,删除无用字段
                foreach ($newItem['lists'] as $version => &$versionItem) {
                    if ($versionItem['downloadList']) {
                        foreach ($versionItem['downloadList'] as &$downloadFile) {
                            if (isset($downloadFile['filethissync'])) unset($downloadFile['filethissync']);
                        }
                    }
                }
                // 合并版本
                $newItem['lists'] = array_merge($oldData['release'][$platform]['lists'] ?? [], $newItem['lists']);
                // 对版本进行排序
                Util::versionSort($newItem['lists']);
                // 合并其他字段,已新版为主
                $newItem = array_merge($oldData['release'][$platform] ?? [], $newItem);
            }
            // 合并此次没有的平台而之前有的其他平台,比如之前同步成功了一个Linux平台的,这一次没有同步成功Linux,只同步了一个Win平台的,老数据Linux平台的不能丢
            $newData['release'] = array_merge($oldData['release'] ?? [], $newData['release']);
        } else {
            unset($newData['release']);
            $newData = array_merge($oldData, $newData);
        }
        return $newData;
    }

    /**
     * 首页列表新老数据合并
     * @param $newData
     * @return mixed
     */
    private function softListMerge($newData)
    {
        // 老数据
        $oldData = [];
        if (file_exists($this->dataDir . '/list.json')) {
            $oldData = json_decode(file_get_contents($this->dataDir . '/list.json'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $oldData = [];
            }
        }

        if ($newData) {
            foreach ($newData as $softName => &$softInfo) {
                $softInfo['latestupdatedate'] = '等待更新';
                $softInfo['latestversion'] = '等待更新';

                // 如果老数据中有latestversion和latestupdatedate字段
                if (isset($oldData[$softName])) {
                    if (isset($oldData[$softName]['latestupdatedate'])) {
                        $softInfo['latestupdatedate'] = $oldData[$softName]['latestupdatedate'];
                    }
                    if (isset($oldData[$softName]['latestversion'])) {
                        $softInfo['latestversion'] = $oldData[$softName]['latestversion'];
                    }
                }

                // 如果是当前软件,尝试从当前数据中提取最新版本
                if ($softName == $this->softName) {
                    //取第一个平台的第一个版本
                    $latestVersionItem = array_shift(array_shift($this->mergedData['release'])['lists']);
                    if ($latestVersionItem) {
                        $softInfo['latestupdatedate'] = date('Y-m-d', $latestVersionItem['timestamp']);
                        $softInfo['latestversion'] = $latestVersionItem['version'];
                    }
                }
            }
        }

        return $newData;
    }
}