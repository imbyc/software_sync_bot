<?php

namespace App\Parser;

use App\Utils\HttpRequest;
use App\Utils\Util;

interface ParserInterface {

    /**
     * 前置解析器
     * @param $data
     * @param $config
     * @return mixed
     */
    function beforeProcess ($data, $config);

    /**
     *  解析器
     * @param $remoteReleaseData
     * @param $platformConfig
     * @return array
     */
    function process ($remoteReleaseData, $platformConfig): ?array;

    /**
     * 后置解析器
     * @param $data
     * @param $rule
     * @return mixed
     */
    function afterProcess ($data, $rule);


    /**
     * 解析版本
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserVersion($data, $rule);

    /**
     * 解析版本更新时间
     * @param $data
     * @param $rule
     * @return array 此函数必须返回数组,且 [原时间格式,原时间格式转为时间戳] 如果没有值就返回 [null,null]
     */
    function parserTime($data, $rule): array;

    /**
     * 解析版本类型,stable 稳定版 unstable 不稳定版
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserChannel($data, $rule);


    /**
     * 解析版本更新说明
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserNotes($data, $rule);

    /**
     * 解析版本新特性
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFeatures($data, $rule);

    /**
     * 解析生成releaseNotes页面的路径
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserGenNotesPagePath ($data, $rule);

    /**
     * 解析下载数据
     * @param $data
     * @param $rule
     * @return array|bool
     */
    function parserDownloadData ($data, $rule): ?array;

    /**
     * 解析文件名称
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileName($data, $rule);

    /**
     * 解析文件Hash
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileHash($data, $rule);

    /**
     * 解析文件大小(单位:字节)
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileSize($data, $rule);

    /**
     * 解析文件下载地址
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileUrl($data, $rule);

    /**
     * 解析文件种类(安装包,压缩包,源码)
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileKind($data, $rule);

    /**
     * 解析文件适用的系统(windows,linux,macos)
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileOS($data, $rule);

    /**
     * 解析文件适用平台(amd64(x86-64), 386(x86-32), arm, arm64, ppc64, ppc64le, mips, mipsle, mips64, mips64le, s390x, wasm)
     * @param $data
     * @param $rule
     * @return mixed
     */
    function parserFileArch($data, $rule);

    /**
     * 获取远程数据
     * @param $link
     * @param $linkType
     * @param string $linkRequestMethod
     * @return mixed
     */
    function getRemoteData ($link, $linkType, $linkRequestMethod);

}