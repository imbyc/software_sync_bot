<?php

namespace App\Utils;

class Sync
{
    /**
     * 同步
     * @param $processedData 解析处理后的数据
     * @param bool $noUpload 不上传,默认为false,置为true,不上传文件只生成数据,可以用于测试
     * @return bool|mixed
     */
    public static function run($processedData, $noUpload = false)
    {
        if (empty($processedData) || !isset($processedData['release']) || empty($processedData['release'])) {
            showLog('没有release数据,无需同步');
            return false;
        }

        $qiniu = new Qiniu();

        // 中转空间
        $transitbucket = systemConfig('bucket.transitbucket');
        // 同步空间
        $syncbucket = systemConfig('bucket.syncbucket');

        foreach ($processedData['release'] as $platform => &$item) {
            if (!isset($item['lists']) || empty($item['lists'])) {
                continue;
            }
            foreach ($item['lists'] as $version => &$versionItem) {
                if (!isset($versionItem['downloadList']) || empty($versionItem['downloadList'])) {
                    continue;
                }
                foreach ($versionItem['downloadList'] as $key => &$downloadItem) {
                    // todo 是否要判断一下url是否是可以下载的链接?
                    if (!isset($downloadItem['fileurl']) || empty($downloadItem['fileurl'])) {
                        continue;
                    }
                    // 判断此版本是否已下载,看一下有没有filekey字段,filekey是保存到七牛云的文件名,
                    // 如果没有这个键,就用 fileuploadprefix + filename
                    $filekey = (isset($downloadItem['filekey']) && !empty($downloadItem['filekey'])) ? $downloadItem['filekey'] : ($downloadItem['fileuploadprefix'] . $downloadItem['filename']);
                    // 从七牛云查找有没有这个文件
                    $isFileExist = false;
                    if ($ret = $qiniu->isFileExist($filekey, $syncbucket)) $isFileExist = true;
                    if (!$isFileExist && !$noUpload) {
                        // 文件不存在,将文件上传到中转空间
                        $ret = $qiniu->fetch($downloadItem['fileurl'], $filekey, $transitbucket);
                        // 如果上传失败,数据移除
                        if (!$ret) {
                            unset($versionItem['downloadList'][$key]);
                            continue;
                        }
                    }

                    // 文件不存在且上传成功($noUpload=true时假设是成功的),标记为此次同步的文件
                    if (!$isFileExist) {
                        $downloadItem['filethissync'] = true;
                    }

                    // 文件已经存在,或者文件上传成功,写入 filesize 和 filekey
                    // 七牛云会返回一个数组, 其中$ret['fsize']表示文件大小
                    // 如果 $downloadItem['filesize']为空,将 $ret['fsize'] 赋给 $downloadItem['filesize'];
                    // $noUpload = true时, 这个值可能没有
                    if ((!isset($downloadItem['filesize']) || empty($downloadItem['filesize'])) && isset($ret['fsize'])) {
                        $downloadItem['filesize'] = $ret['fsize'];
                    }
                    // 如果没有 $downloadItem['filekey'] 将 $filekey 赋给 $downloadItem['filekey']
                    if ((!isset($downloadItem['filekey']) || empty($downloadItem['filekey']))) {
                        $downloadItem['filekey'] = $filekey;
                    }
                }
            }
        }

        return self::afterRun($processedData);

    }

    /**
     * 后续处理
     */
    private static function afterRun($processedData)
    {
        // 在上传失败后 ,会移除 $processedData['release'][平台]['lists'][版本号]['downloadList'][键值]
        // 可能导致 $processedData['release'][平台]['lists'][版本号]['downloadList'] 为空数组,
        // 要将这个版本先移除,如果不移除,将数据保存下来,展示到网页上,会出现没有下载链接无法下载文件
        // 保证最终存储的数据中的版本不会出现无法下载的问题, 这里移除并没有什么问题,因为下次还会再同步
        foreach ($processedData['release'] as $platform => $item) {
            foreach ($item['lists'] as $version => $versionItem) {
                if (empty($versionItem['downloadList'])) {
                    unset($processedData['release'][$platform]['lists'][$version]);
                }
            }
        }

        return $processedData;
    }
}