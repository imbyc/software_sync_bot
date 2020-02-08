<?php

namespace App\Parser;

use App\Utils\HttpRequest;
use App\Utils\Util;

class Parser implements ParserInterface {

    /**
     * @inheritDoc
     */
    public static function beforeProcess()
    {
        // TODO: Implement beforeProcess() method.
    }

    /**
     * @inheritDoc
     */
    public static function afterProcess()
    {
        // TODO: Implement afterProcess() method.
    }

    public static function process ($remoteReleaseData, $parserRule): array {

        $lists = [];

        foreach ($remoteReleaseData as $release) {
            // 版本
            $version = self::parserVersion($release, $parserRule);

            echo '当前处理版本:'.$version.PHP_EOL;

            // 更新时间
            $time = self::parserTime($release, $parserRule);

            // 更新日志
            $notes = self::parserNotes($release, $parserRule);

            // 新特性
            $features = self::parserFeatures($release, $parserRule);

            // 下载信息数据解析
            $downloadList = self::parserDownloadData($release, $parserRule);
            if (!$downloadList) {
                echo '下载地址为空'.PHP_EOL;
                $lists[$version] = false;
            } else {
                // 合并变量到数组
                $lists[$version] = compact('version', 'time', 'notes', 'features', 'downloadList');
                echo '数据正常'.PHP_EOL;
            }

        }

        return $lists;
    }

    /**
     * 解析版本
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserVersion($data, $rule) {
        $versionKey = $rule['version'];
        return Util::arrayDataGet($data, $versionKey);
    }

    /**
     * 解析版本更新时间
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserTime($data, $rule) {
        $timeKey = $rule['time'];
        return Util::arrayDataGet($data, $timeKey);
    }

    /**
     * 解析版本更新说明
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserNotes($data, $rule) {
        $notesKey = $rule['notes'];
        return Util::arrayDataGet($data, $notesKey);
    }

    /**
     * 解析版本新特性
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFeatures($data, $rule) {
        $featuresKey = $rule['features'];
        return Util::arrayDataGet($data, $featuresKey);
    }

    public static function parserDownloadData ($data, $rule): ?array {

        $downloadParserRule = $rule['download'];
        $downloadDataRoot = $downloadParserRule['root'];
        $remoteDownloadData = Util::arrayDataGet($data, $downloadDataRoot);

        $downloadList = [];

        /**
         * $remoteDownloadData 可能会存在未空的情况,一般都是很老的版本,可能没有下载链接,
         * 所以需要判断一下是否为空,为空返回null,表示资源失效
         */
        if (empty($remoteDownloadData)) {
            return null;
        }

        // 是否是索引数组,键名都是数字,表示可能有多个
        if ( !Util::isIndexedArray($remoteDownloadData) ) {
            // 不是索引数组,转为索引数组,方便进行循环
            $remoteDownloadData = [$remoteDownloadData];
        }

        foreach ($remoteDownloadData as $item) {
            // 文件名
            $filename = self::parserFileName($item, $downloadParserRule);

            // 文件大小 (可能需要下载后从文件计算)
            $filesize = self::parserFileSize($item, $downloadParserRule);

            // 文件Hash
            $filehash = self::parserFileHash($item, $downloadParserRule);

            // 文件种类
            $filekind = self::parserFileKind($data, $rule);

            // 文件适用的系统
            $fileos = self::parserFileOS($data, $rule);

            // 文件适用平台
            $filearch = self::parserFileArch($data, $rule);

            // 文件下载链接
            $fileurl = self::parserFileUrl($item, $downloadParserRule);

            // 合并变量到数组
            $downloadList[] = compact('filename', 'filehash', 'filesize', 'fileurl', 'filekind', 'fileos', 'filearch');

        }

        return $downloadList;
    }

    /**
     * 解析文件名称
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileName($data, $rule) {
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
    public static function parserFileHash($data, $rule) {
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
    public static function parserFileSize($data, $rule) {
        $filesizeKey = $rule['filesize'];
        return Util::arrayDataGet($data, $filesizeKey);
    }

    /**
     * 解析文件下载地址
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileUrl($data, $rule) {
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
     * 解析文件种类(安装包,压缩包,源码)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileKind($data, $rule) {

    }

    /**
     * 解析文件适用的系统(windows,linux,macos)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileOS($data, $rule) {

    }

    /**
     * 解析文件适用平台(amd64(x86-64), 386(x86-32), arm, arm64, ppc64, ppc64le, mips, mipsle, mips64, mips64le, s390x, wasm)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileArch($data, $rule) {

    }

    /**
     * 获取远程数据
     * @param $link
     * @param $linkType
     * @param string $linkRequestMethod
     * @return bool|mixed|string
     */
    public static function getRemoteData ($link, $linkType, $linkRequestMethod='GET') {

        if ( strtolower($linkType) == 'json' ) {
            $remoteResponse = HttpRequest::get($link);
            $remoteData = json_decode($remoteResponse, true);

        } else if (strtolower($linkType) == 'html') {
            //TODO 远程发布数据为html
        } else {
            $remoteData = HttpRequest::get($link);
        }

        return $remoteData;

    }
}