<?php

namespace App\Utils;

class Util
{
    public static function formatBytes($size)
    {
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

        $key = is_array($key) ? $key : explode('.', is_int($key) ? (string)$key : $key);
        while (!is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if (!is_array($target)) {
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
     * 列举文件夹的文件
     * @param $path
     * @param string $pattern 可指定某种类型文件, 如 *.txt 所有后缀txt的文件
     * @return array
     */
    public static function searchDir($path, $pattern = '*')
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = [];

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if ($object->isDir()) {
                foreach (glob(rtrim($name, '/') . '/' . $pattern) as $item) {
                    !is_dir($item) && array_push($files, $item);
                }
            }
        }
        foreach (glob(rtrim($path, '/') . '/' . $pattern) as $item) {
            !is_dir($item) && array_push($files, $item);
        }

        return $files;
    }

    /**
     * 对版本列表进行排序
     * 由于允许版本在上传文件时失败,每次只保存已上传成功的数据,
     * 所以等到下一次同步,新数据和老数据合并时,版本可能会乱掉,不是按照从新版到老版的顺序,
     * 展示到页面上就很乱,需要按照版本进行一下排序。
     * 此函数直接对原数据操作，没有返回值
     * @param $versionList
     */
    public static function versionSort(&$versionList)
    {
        uksort($versionList, function ($version1, $version2) {
            // 在第一个参数小于，等于或大于第二个参数时，该比较函数必须相应地返回一个小于，等于或大于 0 的整数。
            // 测试来看是从小到大排序,所以需要除以-1,变成从大到小排序
            // https://www.php.net/manual/zh/function.uksort.php
            return version_compare($version1, $version2) / (-1);
        });
    }

    public static function dateSort(&$logList)
    {
        uksort($logList, function ($date1, $date2) {
            $date1time = strtotime($date1);
            $date2time = strtotime($date2);
            return $date1time == $date2time ? 0 : ($date1time > $date2time ? -1 : 1);
        });
    }

    /**
     * 日期时间转为时间戳
     * @param $datetime
     * @return false|int|null
     */
    public static function datetime2timestamp($datetime)
    {
        if ($timestamp = strtotime($datetime))
            return $timestamp;

        //  todo 如果解析失败,还需要补充其他格式
        return null;
    }

    /**
     * 创建文件夹
     * @param $dir
     * @return bool
     */
    public static function mkdir($dir)
    {
        if (!is_dir($dir)) {
            return mkdir($dir, 0777, true);
        }
        return true;
    }

    /**
     * 写入json文件
     * @param $filePath
     * @param $data
     * @return false|int
     */
    public static function writeJsonFile($filePath, $data)
    {
        return Util::writeFile($filePath, json_encode($data));
    }

    public static function writeFile($filePath, $data)
    {
        Util::mkdir(dirname($filePath));
        $ret = file_put_contents($filePath, $data);
        showLog("生成", $filePath, $ret ? greenSucc() : redFail());
        return $ret;
    }

    /**
     * 变量替换操作
     * @param $arr
     * @return mixed
     * @todo 这个函数还不是很完善,而且可读性很差
     */
    public static function varsReplace($arr)
    {
        array_walk($arr, function (&$v1) use ($arr) {
            if (!$v1) return;
            if (!is_array($v1) && $v1) {
                $v1 = preg_replace_callback('/{{(.*?)}}/im', function ($matches) use ($arr) {
                    return $arr[$matches[1]] ?? '';
                }, $v1);
            } else {
                array_walk($v1, function (&$v2) use ($arr, $v1) {
                    if (!$v2) return;
                    if (!is_array($v2) && $v2) {
                        $v2 = preg_replace_callback('/{{(.*?)}}/im', function ($matches) use ($arr, $v1) {
                            return $v1[$matches[1]] ?? $arr[$matches[1]] ?? '';
                        }, $v2);
                    } else {
                        array_walk($v2, function (&$v3) use ($arr, $v1, $v2) {
                            if (!$v3) return;
                            if (!is_array($v3) && $v3) {
                                $v3 = preg_replace_callback('/{{(.*?)}}/im', function ($matches) use ($arr, $v1, $v2) {
                                    return $v2[$matches[1]] ?? $v1[$matches[1]] ?? $arr[$matches[1]] ?? '';
                                }, $v3);
                            } else {
                                array_walk($v3, function (&$v4) use ($arr, $v1, $v2, $v3) {
                                    if (!$v4) return;
                                    if (!is_array($v4) && $v4) {
                                        $v4 = preg_replace_callback('/{{(.*?)}}/im', function ($matches) use ($arr, $v1, $v2, $v3) {
                                            return $v3[$matches[1]] ?? $v2[$matches[1]] ?? $v1[$matches[1]] ?? $arr[$matches[1]] ?? '';
                                        }, $v4);
                                    } else {
                                        array_walk($v4, function (&$v5) use ($arr, $v1, $v2, $v3, $v4) {
                                            if (!$v5) return;
                                            if (!is_array($v5) && $v5) {
                                                $v5 = preg_replace_callback('/{{(.*?)}}/im', function ($matches) use ($arr, $v1, $v2, $v3, $v4) {
                                                    return $v4[$matches[1]] ?? $v3[$matches[1]] ?? $v2[$matches[1]] ?? $v1[$matches[1]] ?? $arr[$matches[1]] ?? '';
                                                }, $v5);
                                            } else {
                                                // 最多6层
                                                array_walk($v5, function (&$v6) use ($arr, $v1, $v2, $v3, $v4, $v5) {
                                                    $v6 = preg_replace_callback('/{{(.*?)}}/im', function ($matches) use ($arr, $v1, $v2, $v3, $v4, $v5) {
                                                        return $v5[$matches[1]] ?? $v4[$matches[1]] ?? $v3[$matches[1]] ?? $v2[$matches[1]] ?? $v1[$matches[1]] ?? $arr[$matches[1]] ?? '';
                                                    }, $v6);
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });

        return $arr;
    }

    private static function collapse(array $array): array
    {
        $results = [];
        foreach ($array as $values) {
            if (!is_array($values)) {
                continue;
            }
            $results[] = $values;
        }
        return array_merge([], ...$results);
    }


}