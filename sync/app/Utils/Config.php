<?php

namespace App\Utils;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Config {

    private const configDIR=ROOT_PATH.'/config/';

    private $config;

    public function __construct($configFile,$fileExt='yml')
    {
        try {
            if (!file_exists($configFile) || is_dir($configFile)) {
                $configFile = self::configDIR.$configFile.'.'.$fileExt;
            }
            $this->config =  Yaml::parseFile($configFile);
        } catch (ParseException $exception) {
            printf('Unable to parse the YAML string: %s', $exception->getMessage());
            exit;
        }
    }

    /**
     * 获取配置
     * @param null $key 键名,默认为null 取全部配置
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key=null, $default=null) {
        return Util::arrayDataGet($this->config, $key, $default);
    }
}