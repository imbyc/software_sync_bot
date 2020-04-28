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

/**
 * 简单检查一下代理可用性
 * @param $proxy
 * @param $proxyType
 * @return bool
 */
function checkProxy($proxy, $proxyType)
{
    showLog('检查代理可用性');
    $curl = curl_init();
    $checkDomain = [
        'baidu' => 'https://www.baidu.com',
        'google' => 'https://www.google.com',
    ];
    $isUsable = true;
    foreach ($checkDomain as $domainName => $domain) {
        curl_setopt_array($curl, array(
            CURLOPT_URL => $domain,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_PROXY => $proxy,
            CURLOPT_PROXYTYPE => $proxyType,
        ));
        curl_exec($curl);
        $httpCode = curl_getinfo($curl)['http_code'];
        if ($httpCode != 200) $isUsable = false;
        showLog('访问', $domainName, 'HTTP CODE:', $httpCode == 200 ? greenColorText($httpCode) : redColorText($httpCode));
    }
    curl_close($curl);
    if (!$isUsable) showFailLog('代理不可用'); else showSuccLog('代理可用');
    return $isUsable;
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

/**
 * 输出红色的FAIL字符串
 * @return string
 */
function redFail()
{
    return redColorText('FAIL');
}

/**
 * 输出绿色的SUCCESS字符串
 * @return string
 */
function greenSucc()
{
    return greenColorText('SUCCESS');
}