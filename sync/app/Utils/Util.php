<?php

namespace App\Utils;

class Util {
    public static function formatBytes($size) {
        $units = array(' B', ' KB', ' M', ' G', ' T');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }

    public static function arrayDataGet($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', is_int($key) ? (string) $key : $key);
        while (! is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if (! is_array($target)) {
                    return $default;
                }
                $result = [];
                foreach ($target as $item) {
                    $result[] = self::arrayDataGet($item, $key);
                }
                return in_array('*', $key) ? self::collapse($result) : $result;
            }
            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return $default;
            }
        }
        return $target;
    }

    /**
     * 判断数组是否为索引数组
     */
    public static function isIndexedArray($arr)
    {
        if (is_array($arr)) {
            return count(array_filter(array_keys($arr), 'is_string')) === 0;
        }
        return false;
    }

    /**
     * PHP 读取 exe\dll 文件版本号
     *
     * @auth   @腾讯电脑管家(https://zhidao.baidu.com/question/246143241010222924.html)
     * @param  $filename 目标文件
     * @return 读取到的版本号
     */
    public static function getVersionFromFile($filename)
    {
        $fileversion = '';
        $fpFile = @fopen($filename, "rb");
        $strFileContent = @fread($fpFile, filesize($filename));
        fclose($fpFile);
        if($strFileContent)
        {
            $strTagBefore = 'F\0i\0l\0e\0V\0e\0r\0s\0i\0o\0n\0\0\0\0\0';        // 如果使用这行，读取的是 FileVersion
            $strTagBefore = 'P\0r\0o\0d\0u\0c\0t\0V\0e\0r\0s\0i\0o\0n\0\0';    // 如果使用这行，读取的是 ProductVersion
            $strTagAfter = '\0\0';
            if (preg_match("/$strTagBefore(.*?)$strTagAfter/", $strFileContent, $arrMatches))
            {
                if(count($arrMatches) == 2)
                {
                    $fileversion = str_replace("\0", '', $arrMatches[1]);
                }
            }
        }
        return $fileversion;
    }

    /**
     * 获取远程文件大小
     * @param $url
     * @return mixed
     */
    public static function getRemoteFileSize ($url) {
        return get_headers($url, 1)['Content-Length'];

//        $ch = curl_init();
//        curl_setopt_array($ch, array(
//            CURLOPT_URL => $url,
//            CURLOPT_HEADER => true,
//            CURLOPT_NOBODY => true,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_FOLLOWLOCATION => true,
////            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
//        ));
//        $res = curl_exec($ch);
//        print_r($res);
//        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
//        curl_close($ch);
//        return $contentLength;
    }

    private static function collapse(array $array): array
    {
        $results = [];
        foreach ($array as $values) {
            if (! is_array($values)) {
                continue;
            }
            $results[] = $values;
        }
        return array_merge([], ...$results);
    }


}