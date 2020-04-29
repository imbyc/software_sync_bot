<?php

namespace App\Parser;

use App\Utils\Config;
use App\Utils\HttpRequest;
use App\Utils\Util;

class Parser implements ParserInterface
{

    protected $remoteReleaseData;
    protected $parserRule;
    protected $platformConfig;

    /**
     * 前置处理
     * @param $data
     * @param $config
     */
    public function beforeProcess($data, $config)
    {
        $this->platformConfig = $config;

        $this->parserRule = $config['rule'];

        switch (strtolower($this->parserRule['datatype'])) {
            case 'array':
                if (isset($this->parserRule['root'])) {
                    $this->remoteReleaseData = \App\Utils\Util::arrayDataGet($data, $this->parserRule['root']);
                } else {
                    $this->remoteReleaseData = $data;
                }
                break;
            case 'string':
            case 'html':
            case 'xml':
                $this->remoteReleaseData = $data;
                break;
            default:
                $this->remoteReleaseData = $data;
        }

    }

    /**
     * 后置解析
     * 可以对已经解析的数据再次处理
     * 默认直接返回不处理
     * @param $data
     * @param $rule
     */
    public function afterProcess($data, $rule)
    {
        return $data;
    }

    public function process($remoteData, $platformConfig): ?array
    {

        // 前置解析
        $this->beforeProcess($remoteData, $platformConfig);

        // $remoteReleaseData数据类型 ,可选 string , html , array , xml
        switch (strtolower($this->parserRule['datatype'])) {
            case 'array':
                $lists = $this->processArray($this->remoteReleaseData, $this->parserRule);
                break;
            case 'string':
                $lists = $this->processString($this->remoteReleaseData, $this->parserRule);
                break;
            case 'html':
                $lists = $this->processHtml($this->remoteReleaseData, $this->parserRule);
                break;
            case 'xml':
                $lists = $this->processXml($this->remoteReleaseData, $this->parserRule);
                break;
            default:
                $lists = [];
        }

        // 后置解析
        $this->afterProcess($lists, $this->parserRule);

        return $lists;
    }

    /**
     * 解析数组格式数据
     * @param $data
     * @param $rule
     * @return array
     */
    protected function processArray($data, $rule)
    {
        $lists = [];

        // 是否限制版本数量
        if ($getversionmaxnum = $this->platformConfig['getversionmaxnum'] ?? systemConfig('default.getversionmaxnum', 0)) {
            showNoticeLog('getversionmaxnum > 0 取前', $getversionmaxnum, '版本');
            $data = array_slice($data, 0, $getversionmaxnum, true);
        }

        foreach ($data as $release) {
            $lists = array_merge($lists, $this->processItem($release, $rule));
        }
        return $lists;
    }

    /**
     * 解析字符串文本类型数据
     * @param $data
     * @param $rule
     * @return array
     */
    protected function processString($data, $rule)
    {
        return array_merge([], $this->processItem($data, $rule));
    }

    /**
     * 解析xml格式数据
     * @param $data
     * @param $rule
     */
    protected function processXml($data, $rule)
    {
        // todo
    }

    /**
     * 解析html格式数据
     * @param $data
     * @param $rule
     */
    protected function processHtml($data, $rule)
    {
        // todo
    }

    /**
     * 解析单条数据
     * @param $data
     * @param $rule
     * @return array
     */
    protected function processItem($data, $rule)
    {

        $item = [];

        // 版本
        $version = $this->parserVersion($data, $rule);

        // 更新时间
        list($datetime, $timestamp) = $this->parserTime($data, $rule);

        // 更新日志
        $notes = $this->parserNotes($data, $rule);

        // 版本类型
        $channel = $this->parserChannel($data, $rule);

        // 新特性
        $features = $this->parserFeatures($data, $rule);

        // 生成releasenote页面的路径
        $gennotespagepath = $this->parserGenNotesPagePath($data, $rule);

        // 下载信息数据解析
        $downloadList = $this->parserDownloadData($data, $rule);
        if (!$downloadList) {
            showLog('当前处理版本:', $version, '下载地址为空');
            // 对于下载地址为空的不保存数据
//            $item[$version] = false;
        } else {
            // 合并变量到数组
            $item[$version] = compact('version', 'datetime', 'timestamp', 'notes', 'channel', 'features', 'gennotespagepath', 'downloadList');
            showLog('当前处理版本:', $version, '数据正常');
        }

        return $item;

    }

    /**
     * 解析版本
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserVersion($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['version'])) {
            return null;
        }
        $versionKey = $rule['version'];
        return Util::arrayDataGet($data, $versionKey);
    }

    /**
     * 解析版本更新时间
     * @param $data
     * @param $rule
     * @return array 此函数必须返回数组,且 [原时间格式,原时间格式转为时间戳] 如果没有值就返回 [null,null]
     */
    public function parserTime($data, $rule): array
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['time'])) {
            return [null, null];
        }
        $timeKey = $rule['time'];
        //原时间格式
        $datetime = Util::arrayDataGet($data, $timeKey);
        //原时间格式 , 原时间格式转为时间戳
        return [$datetime, Util::datetime2timestamp($datetime)];
    }

    /**
     * 解析版本类型
     * @param $data
     * @param $rule
     * @return array|mixed|null
     */
    public function parserChannel($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['channel'])) {
            return null;
        }

        $notesKey = $rule['channel'];
        return Util::arrayDataGet($data, $notesKey);
    }

    /**
     * 解析版本更新说明
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserNotes($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['notes'])) {
            return null;
        }

        $notesKey = $rule['notes'];
        return Util::arrayDataGet($data, $notesKey);
    }

    /**
     * 解析版本新特性
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFeatures($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['features'])) {
            return null;
        }

        $featuresKey = $rule['features'];
        return Util::arrayDataGet($data, $featuresKey);
    }

    /**
     * 解析生成releaseNotes页面的路径
     * @param $data
     * @param $rule
     * @return array|mixed|null
     */
    public function parserGenNotesPagePath($data, $rule)
    {
        // 如果没有这个键值就返回默认值,先从配置中取,如果没有就给默认值 app/{{softname}}/{{platform}}/{{version}}/releasenotes.html
        if (!isset($rule['gennotespagepath'])) {
            return systemConfig('default.gennotespagepath', 'app/{{softname}}/{{platform}}/{{version}}/releasenotes.html');
        }

        return $rule['gennotespagepath'];
    }

    public function parserDownloadData($data, $rule): ?array
    {

        // 如果没有这个键值就返回null
        if (!isset($rule['download'])) {
            return null;
        }
        // [release][平台][rule][download]
        $downloadParserRule = $rule['download'];

        $downloadList = [];

        // string 远程数据是字符串
        // array 远程数据是数组,循环去获取
        switch ($this->parserRule['datatype']) {
            case "array":
                $downloadList = $this->parserArrayRemoteDownloadData($data, $downloadParserRule);
                break;
            case "string":
                $downloadList = $this->parserStringRemoteDownloadData($data, $downloadParserRule);
                break;
            default:
                break;
        }

        return $downloadList;
    }

    /**
     * 解析远程Download数据为数组的文件
     * @param $data
     * @param $downloadParserRule
     * @return array|null
     */
    private function parserArrayRemoteDownloadData($data, $downloadParserRule)
    {
        $downloadDataRoot = $downloadParserRule['root'];
        $remoteDownloadData = Util::arrayDataGet($data, $downloadDataRoot);

        /**
         * $remoteDownloadData 可能会存在为空的情况,一般都是很老的版本,可能没有下载链接,
         * 如PHpstorm一些很老的版本,官方release有版本信息,但没有给下载链接
         * 所以需要判断一下是否为空,为空返回null,表示资源失效
         */
        if (empty($remoteDownloadData)) {
            return null;
        }

        // 是否是索引数组,键名都是数字,表示可能有多个
        if (!Util::isIndexedArray($remoteDownloadData)) {
            // 不是索引数组,转为索引数组,方便进行循环
            $remoteDownloadData = [$remoteDownloadData];
        }

        $downloadList = [];

        foreach ($remoteDownloadData as $item) {
            if ($singleFileInfo = $this->parserFile($item, $downloadParserRule)) {
                $downloadList[] = $singleFileInfo;
            }
        }

        return $downloadList;
    }

    /**
     * 解析远程数据为空的文件
     * @param $data
     * @param $downloadParserRule
     * @return array|null
     */
    private function parserStringRemoteDownloadData($data, $downloadParserRule)
    {
        $downloadList = [];
        if ($singleFileInfo = $this->parserFile($data, $downloadParserRule)) {
            $downloadList[] = $singleFileInfo;
        }
        return $downloadList;
    }

    /**
     * 解析单个文件
     * @param $item
     * @param $downloadParserRule
     * @return array|null
     */
    private function parserFile($item, $downloadParserRule)
    {
        // 文件下载链接
        $fileurl = $this->parserFileUrl($item, $downloadParserRule);

        // 文件下载链接很重要,如果没有文件下载链接,就下载不了文件,直接跳过
        if (empty($fileurl)) {
            return null;
        }

        // 文件名
        $filename = $this->parserFileName($item, $downloadParserRule);

        // 文件大小 (可能需要下载后从文件计算)
        $filesize = $this->parserFileSize($item, $downloadParserRule);

        // 文件Hash
        $filehash = $this->parserFileHash($item, $downloadParserRule);

        // 文件种类
        $filekind = $this->parserFileKind($item, $downloadParserRule);

        // 文件适用的系统
        $fileos = $this->parserFileOS($item, $downloadParserRule);

        // 文件适用平台
        $filearch = $this->parserFileArch($item, $downloadParserRule);

        // 文件上传时路径前缀
        $fileuploadprefix = $this->parserFileUploadPrefix($item, $downloadParserRule);

        // 合并变量到数组
        return compact('filename', 'filehash', 'filesize', 'fileurl', 'filekind', 'fileos', 'filearch', 'fileuploadprefix');

    }

    /**
     * 解析文件下载地址
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileUrl($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['fileurl'])) {
            return null;
        }

        $fileurlKey = $rule['fileurl'];
        switch (strtolower($fileurlKey['type'])) {
            case 'field':
                $fileurl = Util::arrayDataGet($data, $fileurlKey['value']);
                break;
            case 'string':
                $fileurl = $fileurlKey['value'];
                break;
            default:
                $fileurl = Util::arrayDataGet($data, $fileurlKey['value']);
                break;
        }
        return $fileurl;
    }

    /**
     * 解析文件名称
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileName($data, $rule)
    {
        $filenameKey = $rule['filename'];
        if (!isset($filenameKey['type'])) {
            if (isset($filenameKey['value'])) {
                $filename = Util::arrayDataGet($data, $filenameKey['value']);
            } else {
                $filename = Util::arrayDataGet($data, $filenameKey);
            }
        } else {
            switch ($filenameKey['type']) {
                case 'field':
                    $filename = Util::arrayDataGet($data, $filenameKey['value']);
                    break;
                case 'path':
                    $filename = pathinfo(Util::arrayDataGet($data, $filenameKey['value']))['basename'];
                    break;
                case 'string':
                    $filename = $filenameKey['value'];
                    break;
                default:
                    $filename = Util::arrayDataGet($data, $filenameKey['value']);
                    break;
            }
        }

        return $filename;
    }

    /**
     * 解析文件Hash
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileHash($data, $rule)
    {

        // 如果没有这个键值就返回null
        if (!isset($rule['filehash'])) {
            return null;
        }

        $filehashKey = $rule['filehash'];
        if (!isset($filehashKey['type'])) {
            if (isset($filehashKey['value'])) {
                $filehash = Util::arrayDataGet($data, $filehashKey['value']);
            } else {
                $filehash = Util::arrayDataGet($data, $filehashKey);
            }
        } else {
            switch ($filehashKey['type']) {
                case 'url':
                    $hashUrl = Util::arrayDataGet($data, $filehashKey['value']);
                    showLog('从远程文件解析 filehash');
                    $response = HttpRequest::get($hashUrl);
                    $filehash = explode($filehashKey['delimiter'], $response)[0];
                    break;
                default:
                    $filehash = Util::arrayDataGet($data, $filehashKey['value']);
                    break;
            }
        }

        return $filehash;
    }

    /**
     * 解析文件大小(单位:字节)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileSize($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['filesize'])) {
            return null;
        }
        $filesizeKey = $rule['filesize'];
        return Util::arrayDataGet($data, $filesizeKey);
    }

    /**
     * 解析文件种类(安装包,压缩包,源码)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileKind($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['filekind'])) {
            return null;
        }

        $filekindKey = $rule['filekind'];
        switch (strtolower($filekindKey['type'])) {
            case 'field':
                $filekind = Util::arrayDataGet($data, $filekindKey['value']);
                break;
            case 'string':
                $filekind = $filekindKey['value'];
                break;
            default:
                $filekind = Util::arrayDataGet($data, $filekindKey['value']);
                break;
        }
        return $filekind;
    }

    /**
     * 解析文件适用的系统(windows,linux,macos)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileOS($data, $rule)
    {
        // 如果没有这个键值就返回platform
        if (!isset($rule['fileos'])) {
            return $this->platformConfig['platform'];
        }

        $fileosKey = $rule['fileos'];
        switch (strtolower($fileosKey['type'])) {
            case 'field':
                $fileos = Util::arrayDataGet($data, $fileosKey['value']);
                break;
            case 'string':
                $fileos = $fileosKey['value'];
                break;
            default:
                $fileos = Util::arrayDataGet($data, $fileosKey['value']);
                break;
        }
        return $fileos;
    }

    /**
     * 解析文件适用平台(amd64(x86-64), 386(x86-32), arm, arm64, ppc64, ppc64le, mips, mipsle, mips64, mips64le, s390x, wasm)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public function parserFileArch($data, $rule)
    {
        // 如果没有这个键值就返回null
        if (!isset($rule['filearch'])) {
            return null;
        }

        $filearchKey = $rule['filearch'];
        switch (strtolower($filearchKey['type'])) {
            case 'field':
                $filearch = Util::arrayDataGet($data, $filearchKey['value']);
                break;
            case 'string':
                $filearch = $filearchKey['value'];
                break;
            default:
                $filearch = Util::arrayDataGet($data, $filearchKey['value']);
                break;
        }
        return $filearch;
    }

    /**
     * 解析上传文件前缀
     * @param $data
     * @param $rule
     * @return array|mixed|null
     */
    public function parserFileUploadPrefix($data, $rule)
    {
        // 如果没有这个键值就返回默认值,先从配置中取,如果没有就给默认值 app/{{softname}}/{{version}}/
        if (!isset($rule['fileuploadprefix'])) {
            return systemConfig('default.fileuploadprefix', 'app/{{softname}}/{{version}}/');
        }

        $fileuploadprefixKey = $rule['fileuploadprefix'];
        switch (strtolower($fileuploadprefixKey['type'])) {
            case 'field':
                $fileuploadprefix = Util::arrayDataGet($data, $fileuploadprefixKey['value']);
                break;
            case 'string':
                $fileuploadprefix = $fileuploadprefixKey['value'];
                break;
            default:
                $fileuploadprefix = Util::arrayDataGet($data, $fileuploadprefixKey['value']);
                break;
        }
        return $fileuploadprefix;
    }

    /**
     * 获取远程数据
     * @param $link
     * @param $linkType
     * @param string $linkRequestMethod
     * @return bool|mixed|string
     */
    public function getRemoteData($link, $linkType, $linkRequestMethod = 'GET')
    {

        switch (strtoupper($linkRequestMethod)) {
            case 'POST':
                $remoteResponse = HttpRequest::post($link);
                break;
            default:
                $remoteResponse = HttpRequest::get($link);
        }

        switch (strtolower($linkType)) {
            case 'json':
                $remoteData = json_decode($remoteResponse, true);
                break;
            case 'html':
            case 'txt':
            case 'xml':
                $remoteData = $remoteResponse;
                break;
            default:
                $remoteData = $remoteResponse;
        }

        return $remoteData;
    }


}