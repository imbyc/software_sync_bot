<?php


/**
 * 获取系统配置
 * @param $key
 * @param null $default
 * @return mixed
 */
function systemConfig($key, $default = null)
{
    return $GLOBALS['systemConfig']->get($key, $default);
}

//====================下面几个是日志输出函数,没啥用============================//
//====================下面几个是日志输出函数,没啥用============================//
//====================下面几个是日志输出函数,没啥用============================//

/**
 * 通用日志
 * @param mixed ...$params
 */
function showLog(...$params)
{
    echo sprintLog('==> ', ...$params);
}

/**
 * 失败日志
 * @param mixed ...$params
 */
function showFailLog(...$params)
{
    echo redColorText(sprintLog('❌ ', ...$params));
}

/**
 * 成功日志
 * @param mixed ...$params
 */
function showSuccLog(...$params)
{
    echo greenColorText(sprintLog('✔ ', ...$params));
}

/**
 * 提示日志
 * @param mixed ...$params
 */
function showNoticeLog(...$params)
{
    echo yellowColorText(sprintLog('✔ ', ...$params));
}


function sprintLog(...$params)
{
    $l = '';
    foreach ($params as $v) {
        $l .= is_array($v) ? (PHP_EOL . print_r($v, true) . PHP_EOL) : print_r($v, true) . ' ';
    }
    return $l . PHP_EOL;
}

/**
 * 给文本加上红色在命令行显示
 * @param $text
 * @return string
 */
function redColorText($text)
{
    return "\033[31m" . $text . "\033[0m";
}

/**
 * 给文本加上绿色在命令行显示
 * @param $text
 * @return string
 */
function greenColorText($text)
{
    return "\033[32m" . $text . "\033[0m";
}

/**
 * 给文本加上黄色在命令行显示
 * @param $text
 * @return string
 */
function yellowColorText($text)
{
    return "\033[33m" . $text . "\033[0m";
}

function redFail()
{
    return redColorText('FAIL');
}

function greenSucc()
{
    return greenColorText('SUCCESS');
}