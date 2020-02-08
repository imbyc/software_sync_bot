<?php

namespace App\Parser;

use App\Utils\HttpRequest;
use App\Utils\Util;

interface ParserInterface {

    /**
     * 前置解析器
     * @return mixed
     */
    public static function beforeProcess ();

    /**
     *  解析器
     * @param $remoteReleaseData
     * @param $parserRule
     * @return array
     */
    public static function process ($remoteReleaseData, $parserRule): array;

    /**
     * 后置解析器
     * @return mixed
     */
    public static function afterProcess ();


    /**
     * 解析版本
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserVersion($data, $rule);

    /**
     * 解析版本更新时间
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserTime($data, $rule);

    /**
     * 解析版本更新说明
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserNotes($data, $rule);

    /**
     * 解析版本新特性
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFeatures($data, $rule);

    /**
     * 解析下载数据
     * @param $data
     * @param $rule
     * @return array|bool
     */
    public static function parserDownloadData ($data, $rule): ?array;

    /**
     * 解析文件名称
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileName($data, $rule);

    /**
     * 解析文件Hash
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileHash($data, $rule);

    /**
     * 解析文件大小(单位:字节)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileSize($data, $rule);

    /**
     * 解析文件下载地址
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileUrl($data, $rule);

    /**
     * 解析文件种类(安装包,压缩包,源码)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileKind($data, $rule);

    /**
     * 解析文件适用的系统(windows,linux,macos)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileOS($data, $rule);

    /**
     * 解析文件适用平台(amd64(x86-64), 386(x86-32), arm, arm64, ppc64, ppc64le, mips, mipsle, mips64, mips64le, s390x, wasm)
     * @param $data
     * @param $rule
     * @return mixed
     */
    public static function parserFileArch($data, $rule);

    /**
     * 获取远程数据
     * @param $link
     * @param $linkType
     * @param string $linkRequestMethod
     * @return mixed
     */
    public static function getRemoteData ($link, $linkType, $linkRequestMethod='GET');


}